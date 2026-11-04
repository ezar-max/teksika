<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenStreetMapGeocodingService
{
    public function reverse(float $latitude, float $longitude): ?array
    {
        $apiKey = trim((string) config('services.google_maps.api_key', ''));
        $cachePrefix = $apiKey !== '' ? 'gmaps_nominatim_address_v3' : 'nominatim_address_v2';
        $cacheKey = sprintf('%s_%.5f_%.5f', $cachePrefix, $latitude, $longitude);
        $cachedAddress = Cache::get($cacheKey);
        if ($cachedAddress !== null) {
            return $cachedAddress;
        }

        // 1. Provider Utama: Google Maps Places API (Nearby Search)
        if ($apiKey !== '') {
            $googleResult = $this->reverseGooglePlaces($latitude, $longitude, $apiKey);
            if ($googleResult !== null) {
                // Jika jalan spesifik atau RT/RW belum lengkap di Google Places, perkaya dengan fallback Nominatim bila tersedia
                if (($googleResult['jalan'] === '' || $googleResult['rt'] === '' || $googleResult['rw'] === '')
                    && Cache::add('nominatim_public_request', true, 1)) {
                    $osmFallback = $this->reverseNominatim($latitude, $longitude);
                    if ($osmFallback !== null) {
                        if ($googleResult['jalan'] === '' && $osmFallback['jalan'] !== '') {
                            $googleResult['jalan'] = $osmFallback['jalan'];
                        }
                        if ($googleResult['kabupaten'] === '' && $osmFallback['kabupaten'] !== '') {
                            $googleResult['kabupaten'] = $osmFallback['kabupaten'];
                        }
                        if ($googleResult['kecamatan'] === '' && $osmFallback['kecamatan'] !== '') {
                            $googleResult['kecamatan'] = $osmFallback['kecamatan'];
                        }
                        if ($googleResult['rt'] === '' && $osmFallback['rt'] !== '') {
                            $googleResult['rt'] = $osmFallback['rt'];
                        }
                        if ($googleResult['rw'] === '' && $osmFallback['rw'] !== '') {
                            $googleResult['rw'] = $osmFallback['rw'];
                        }
                    }
                }

                Cache::put($cacheKey, $googleResult, 2592000);
                return $googleResult;
            }
        }

        // 2. Fallback: Nominatim OpenStreetMap
        if (!Cache::add('nominatim_public_request', true, 1)) {
            return null;
        }

        $result = $this->reverseNominatim($latitude, $longitude);
        if ($result !== null) {
            Cache::put($cacheKey, $result, 2592000);
        }

        return $result;
    }

    private function reverseGooglePlaces(float $latitude, float $longitude, string $apiKey): ?array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'places.displayName,places.formattedAddress,places.addressComponents,places.location',
            ])->timeout(5)->post('https://places.googleapis.com/v1/places:searchNearby', [
                'locationRestriction' => [
                    'circle' => [
                        'center' => [
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                        ],
                        'radius' => 300.0,
                    ],
                ],
                'rankPreference' => 'DISTANCE',
                'maxResultCount' => 5,
                'languageCode' => 'id',
            ]);

            if (!$response->successful()) {
                return null;
            }

            $places = $response->json('places', []);
            if (!is_array($places) || empty($places)) {
                return null;
            }

            $kabupaten = '';
            $kecamatan = '';
            $jalan = '';
            $kelurahan = '';
            $contextParts = [];

            foreach ($places as $place) {
                if (!empty($place['formattedAddress'])) {
                    $contextParts[] = $place['formattedAddress'];
                }
                if (!empty($place['displayName']['text'])) {
                    $contextParts[] = $place['displayName']['text'];
                }

                $components = $place['addressComponents'] ?? [];
                foreach ($components as $component) {
                    $types = $component['types'] ?? [];
                    $longText = trim((string) ($component['longText'] ?? ''));
                    if ($longText === '') {
                        continue;
                    }

                    if ($kabupaten === '' && in_array('administrative_area_level_2', $types, true)) {
                        $kabupaten = $longText;
                    }
                    if ($kecamatan === '' && in_array('administrative_area_level_3', $types, true)) {
                        $kecamatan = preg_replace('/^(?:Kecamatan|Kec\.)\s+/iu', '', $longText) ?? $longText;
                    }
                    if ($jalan === '' && in_array('route', $types, true)) {
                        $jalan = $longText;
                    }
                    if ($kelurahan === '' && (in_array('administrative_area_level_4', $types, true) || in_array('sublocality_level_1', $types, true))) {
                        $kelurahan = $longText;
                    }
                }
            }

            if ($jalan === '' && $kelurahan !== '') {
                $jalan = $kelurahan;
            }

            $addressContext = implode(' ', $contextParts);
            $result = [
                'kabupaten' => $kabupaten,
                'kecamatan' => $kecamatan,
                'jalan' => $jalan,
                'rt' => $this->extractRtRw('RT', null, $addressContext),
                'rw' => $this->extractRtRw('RW', null, $addressContext),
                'provider' => 'google_maps',
            ];

            if ($kabupaten === '' && $kecamatan === '' && $jalan === '') {
                return null;
            }

            return $result;
        } catch (\Throwable $exception) {
            Log::info('Google Maps Places reverse lookup failed, falling back to OpenStreetMap.');
            return null;
        }
    }

    private function reverseNominatim(float $latitude, float $longitude): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'TEKSIKA/1.0 (OpenStreetMap reverse geocoding)',
                'Accept-Language' => 'id',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
                'addressdetails' => 1,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $address = $response->json('address', []);
            $addressContext = implode(' ', array_filter(array_merge(
                array_values($address),
                [$response->json('display_name', '')]
            )));
            $result = [
                'kabupaten' => $address['county'] ?? $address['city'] ?? $address['town'] ?? $address['state_district'] ?? '',
                'kecamatan' => $address['city_district'] ?? $address['municipality'] ?? $address['district'] ?? $address['suburb'] ?? '',
                'jalan' => $address['road'] ?? $address['pedestrian'] ?? $address['residential'] ?? '',
                'rt' => $this->extractRtRw('RT', $address['rt'] ?? null, $addressContext),
                'rw' => $this->extractRtRw('RW', $address['rw'] ?? null, $addressContext),
            ];

            if (implode('', $result) === '') {
                return null;
            }

            return $result;
        } catch (\Throwable $exception) {
            Log::info('OpenStreetMap reverse geocoding request failed.');

            return null;
        }
    }

    public function forward(string $query): ?array
    {
        $query = trim($query);
        $apiKey = trim((string) config('services.google_maps.api_key', ''));
        $cachePrefix = $apiKey !== '' ? 'gmaps_nominatim_search_' : 'nominatim_search_';
        $cacheKey = $cachePrefix . sha1(mb_strtolower($query));
        $cachedLocation = Cache::get($cacheKey);
        if ($cachedLocation !== null) {
            return $cachedLocation;
        }

        $location = null;

        // 1. Provider Utama: Google Maps Places Text Search
        if ($apiKey !== '') {
            $location = $this->searchGooglePlaces($query, $apiKey);
        }

        // 2. Fallback: Nominatim OpenStreetMap
        if ($location === null && Cache::add('nominatim_public_request', true, 1)) {
            $location = $this->searchNominatim($query);
        }

        // 3. Fallback: Photon Komoot
        if ($location === null) {
            $location = $this->searchPhoton($query);
        }

        if ($location !== null) {
            Cache::put($cacheKey, $location, 2592000);
            return $location;
        }

        return null;
    }

    private function searchGooglePlaces(string $query, string $apiKey): ?array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'places.displayName,places.formattedAddress,places.location',
            ])->timeout(5)->post('https://places.googleapis.com/v1/places:searchText', [
                'textQuery' => $query,
                'languageCode' => 'id',
                'regionCode' => 'ID',
                'maxResultCount' => 1,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $place = $response->json('places.0');
            $lat = $place['location']['latitude'] ?? null;
            $lng = $place['location']['longitude'] ?? null;

            if (!is_numeric($lat) || !is_numeric($lng)) {
                return null;
            }

            return [
                'latitude' => (float) $lat,
                'longitude' => (float) $lng,
                'display_name' => (string) ($place['formattedAddress'] ?? $place['displayName']['text'] ?? ''),
                'provider' => 'google_maps',
            ];
        } catch (\Throwable $exception) {
            Log::info('Google Maps Places forward geocoding failed, falling back to OpenStreetMap.');
            return null;
        }
    }

    private function searchNominatim(string $query): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'TEKSIKA/1.0 (OpenStreetMap geocoding)',
                'Accept-Language' => 'id',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'jsonv2',
                'q' => $query,
                'countrycodes' => 'id',
                'limit' => 1,
                'addressdetails' => 1,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $location = $response->json('0');
            if (!is_array($location) || !is_numeric($location['lat'] ?? null) || !is_numeric($location['lon'] ?? null)) {
                return null;
            }

            return [
                'latitude' => (float) $location['lat'],
                'longitude' => (float) $location['lon'],
                'display_name' => (string) ($location['display_name'] ?? ''),
                'provider' => 'nominatim',
            ];
        } catch (\Throwable $exception) {
            Log::info('OpenStreetMap Nominatim forward geocoding request failed.');

            return null;
        }
    }

    private function searchPhoton(string $query): ?array
    {
        try {
            $photonQuery = preg_replace('/\b(?:Kecamatan|Kabupaten|Kota)\s+/iu', '', $query) ?? $query;
            $response = Http::withHeaders([
                'User-Agent' => 'TEKSIKA/1.0 (OpenStreetMap geocoding)',
                'Accept-Language' => 'id',
            ])->timeout(5)->get('https://photon.komoot.io/api/', [
                'q' => $photonQuery,
                'lang' => 'id',
                'limit' => 1,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $feature = $response->json('features.0');
            $coordinates = $feature['geometry']['coordinates'] ?? null;
            if (!is_array($coordinates) || !is_numeric($coordinates[0] ?? null) || !is_numeric($coordinates[1] ?? null)) {
                return null;
            }

            $properties = $feature['properties'] ?? [];
            $displayName = implode(', ', array_filter([
                $properties['name'] ?? null,
                $properties['street'] ?? null,
                $properties['district'] ?? null,
                $properties['city'] ?? null,
                $properties['country'] ?? null,
            ]));

            return [
                'latitude' => (float) $coordinates[1],
                'longitude' => (float) $coordinates[0],
                'display_name' => $displayName,
                'provider' => 'photon',
            ];
        } catch (\Throwable $exception) {
            Log::info('OpenStreetMap Photon forward geocoding request failed.');

            return null;
        }
    }

    private function extractRtRw(string $label, mixed $explicitValue, string $addressContext): string
    {
        $explicitValue = trim((string) ($explicitValue ?? ''));
        if (preg_match('/^\d{1,3}$/', $explicitValue)) {
            return $explicitValue;
        }

        $pattern = '/\b' . preg_quote($label, '/') . '\s*[:.]?\s*(\d{1,3})\b/i';
        if (preg_match($pattern, $explicitValue . ' ' . $addressContext, $matches)) {
            return $matches[1];
        }

        return '';
    }
}
