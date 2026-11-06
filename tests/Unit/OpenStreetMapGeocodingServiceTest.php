<?php

namespace Tests\Unit;

use App\Services\OpenStreetMapGeocodingService;
use App\Services\HaversineService;
use App\Http\Controllers\User\ServiceRequestController;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenStreetMapGeocodingServiceTest extends TestCase
{
    public function test_request_address_endpoint_accepts_one_full_address_and_retries_without_rt_rw(): void
    {
        $address = 'Jalan Pancasila, RT 004/RW 002, Kecamatan Gunung Putri, Kabupaten Bogor';
        $location = [
            'latitude' => -6.345678,
            'longitude' => 106.987654,
            'display_name' => 'Jalan Pancasila, Gunung Putri, Kabupaten Bogor',
            'provider' => 'photon',
        ];
        $geocoder = \Mockery::mock(OpenStreetMapGeocodingService::class);
        $geocoder->shouldReceive('forward')
            ->once()
            ->with($address . ', Indonesia')
            ->andReturn(null);
        $geocoder->shouldReceive('forward')
            ->once()
            ->with('Jalan Pancasila, Kecamatan Gunung Putri, Kabupaten Bogor, Indonesia')
            ->andReturn($location);
        $request = Request::create(
            '/request/lokasi-alamat',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['alamat' => $address])
        );

        $response = (new ServiceRequestController(app(HaversineService::class)))
            ->locateAddress($request, $geocoder);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($location, $response->getData(true)['location']);
    }

    public function test_it_uses_google_geocoding_to_find_coordinates_from_an_address(): void
    {
        Cache::flush();
        config(['services.google_maps.api_key' => 'test-google-key']);
        Http::fake([
            'maps.googleapis.com/maps/api/geocode/json*' => Http::response([
                'status' => 'OK',
                'results' => [[
                    'formatted_address' => 'Jalan Melati, Kabupaten Bogor, Indonesia',
                    'geometry' => [
                        'location' => [
                            'lat' => -6.345678,
                            'lng' => 106.987654,
                        ],
                    ],
                ]],
            ]),
        ]);

        $query = 'Jalan Melati, Kecamatan Gunung Putri, Kabupaten Bogor, Indonesia';
        $location = (new OpenStreetMapGeocodingService())->forward($query);

        $this->assertSame([
            'latitude' => -6.345678,
            'longitude' => 106.987654,
            'display_name' => 'Jalan Melati, Kabupaten Bogor, Indonesia',
            'provider' => 'google_maps',
        ], $location);
        Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'maps.googleapis.com/maps/api/geocode/json')
            && $request['address'] === $query
            && $request['key'] === 'test-google-key'
            && $request['region'] === 'id');
        Http::assertSentCount(1);
    }

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
