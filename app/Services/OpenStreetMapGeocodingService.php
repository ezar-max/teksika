<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenStreetMapGeocodingService
{
    public function reverse(float $latitude, float $longitude): ?array
    {
        $cacheKey = sprintf('nominatim_address_v2_%.5f_%.5f', $latitude, $longitude);
        $cachedAddress = Cache::get($cacheKey);
        if ($cachedAddress !== null) {
            return $cachedAddress;
        }

        if (!Cache::add('nominatim_public_request', true, 1)) {
            return null;
        }

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

            Cache::put($cacheKey, $result, 2592000);

            return $result;
        } catch (\Throwable $exception) {
            Log::info('OpenStreetMap reverse geocoding request failed.');

            return null;
        }
    }

    public function forward(string $query): ?array
    {
        $query = trim($query);
        $cacheKey = 'nominatim_search_' . sha1(mb_strtolower($query));
        $cachedLocation = Cache::get($cacheKey);
        if ($cachedLocation !== null) {
            return $cachedLocation;
        }

        $location = null;
        if (Cache::add('nominatim_public_request', true, 1)) {
            $location = $this->searchNominatim($query);
        }

        if ($location === null) {
            $location = $this->searchPhoton($query);
        }

        if ($location !== null) {
            $result = $location;
            Cache::put($cacheKey, $result, 2592000);

            return $result;
        }

        return null;
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