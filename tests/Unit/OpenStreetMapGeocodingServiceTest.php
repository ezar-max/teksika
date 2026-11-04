<?php

namespace Tests\Unit;

use App\Services\OpenStreetMapGeocodingService;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenStreetMapGeocodingServiceTest extends TestCase
{
    public function test_it_finds_coordinates_from_an_address_and_caches_the_result(): void
    {
        Cache::flush();
        Http::fake([
            'nominatim.openstreetmap.org/search*' => Http::response([
                [
                    'lat' => '-6.345678',
                    'lon' => '106.987654',
                    'display_name' => 'Jalan Melati, Gunung Putri, Indonesia',
                ],
            ]),
        ]);

        $geocoder = new OpenStreetMapGeocodingService();
        $query = 'Jalan Melati, Kecamatan Gunung Putri, Kabupaten Bogor, Indonesia';

        $this->assertSame([
            'latitude' => -6.345678,
            'longitude' => 106.987654,
            'display_name' => 'Jalan Melati, Gunung Putri, Indonesia',
            'provider' => 'nominatim',
        ], $geocoder->forward($query));

        $this->assertSame(
            [
                'latitude' => -6.345678,
                'longitude' => 106.987654,
                'display_name' => 'Jalan Melati, Gunung Putri, Indonesia',
                'provider' => 'nominatim',
            ],
            $geocoder->forward($query)
        );
        Http::assertSentCount(1);
        Http::assertSent(fn (ClientRequest $request) => $request['q'] === $query && $request['countrycodes'] === 'id');
    }

    public function test_it_uses_photon_when_nominatim_does_not_find_the_street(): void
    {
        Cache::flush();
        Http::fake([
            'nominatim.openstreetmap.org/search*' => Http::response([]),
            'photon.komoot.io/api/*' => Http::response([
                'features' => [[
                    'geometry' => [
                        'coordinates' => [106.987654, -6.345678],
                    ],
                    'properties' => [
                        'name' => 'Jalan Melati',
                        'district' => 'Gunung Putri',
                        'city' => 'Kabupaten Bogor',
                        'country' => 'Indonesia',
                    ],
                ]],
            ]),
        ]);

        $query = 'Jalan Melati, Kecamatan Gunung Putri, Kabupaten Bogor, Indonesia';
        $location = (new OpenStreetMapGeocodingService())->forward($query);

        $this->assertSame([
            'latitude' => -6.345678,
            'longitude' => 106.987654,
            'display_name' => 'Jalan Melati, Gunung Putri, Kabupaten Bogor, Indonesia',
            'provider' => 'photon',
        ], $location);
        Http::assertSentCount(2);
        Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'photon.komoot.io')
            && $request['q'] === 'Jalan Melati, Gunung Putri, Bogor, Indonesia'
            && $request['lang'] === 'id');
    }
}
