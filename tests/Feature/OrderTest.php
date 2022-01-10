<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderTest extends TestCase
{
    /**
     * Asserts that input data for orders is validated.
     *
     * @return void
     */
    public function testItValidatesInput()
    {
        // Data with HMO that doesn't exist
        $payload = [
            'order' => [
                [
                    "item" => "fkuydgyfid",
                    "unit_price" => "2",
                    "qty" => "2",
                    "sub_total" => 4,
                ],
                [
                    "item" => "ghudhiugi",
                    "unit_price" => "6",
                    "qty" => "3",
                    "sub_total" => 18,
                ],
            ],
            'provider_data' => [
                "hmo_code" => "HMO-G",
                "provider_name" => "Kingsley",
                "date" => "2021-11-18",
            ],
            'total' => 22,
        ];

        $this->json('post', '/api/create', $payload)
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "provider_data.hmo_code" => [
                        "The selected provider data.hmo code is invalid.",
                    ],
                ],
            ]);
    }

    /**
     * Asserts that orders can be created.
     *
     * @return void
     */
    public function testItCanCreateOrder()
    {
        $payload = [
            'order' => [
                [
                    "item" => "fkuydgyfid",
                    "unit_price" => "2",
                    "qty" => "2",
                    "sub_total" => 4,
                ],
                [
                    "item" => "ghudhiugi",
                    "unit_price" => "6",
                    "qty" => "3",
                    "sub_total" => 18,
                ],
            ],
            'provider_data' => [
                "hmo_code" => "HMO-B",
                "provider_name" => "Kingsley",
                "date" => "2021-11-18",
            ],
            'total' => 22,
        ];

        $this->json('post', '/api/create', $payload)
            ->assertStatus(200)
            ->assertJson([
                "success" => true,
                "message" => 'Order created successfully!',
            ]);
    }

    /**
     * Asserts that batches can be created by encounter date
     *
     * @return void
     */
    public function testItCanCreateBatchByEncounterDate()
    {
        $payload = [
            'order' => [
                [
                    "item" => "fkuydgyfid",
                    "unit_price" => "2",
                    "qty" => "2",
                    "sub_total" => 4,
                ],
                [
                    "item" => "ghudhiugi",
                    "unit_price" => "6",
                    "qty" => "3",
                    "sub_total" => 18,
                ],
            ],
            'provider_data' => [
                "hmo_code" => "HMO-B",
                "provider_name" => "Ben",
                "date" => "2021-11-18", // Encounter date
            ],
            'total' => 22,
        ];

        $this->json('post', '/api/create', $payload)
            ->assertStatus(200);

        $this->get(route('batch', ['HMO-B', 'Ben', '2021-11-08']))
            ->assertStatus(200);
    }

    /**
     * Asserts that batches can be created by sent date
     *
     * @return void
     */
    public function testItCanCreateBatchBySentDate()
    {
        $payload = [
            'order' => [
                [
                    "item" => "fkuydgyfid",
                    "unit_price" => "2",
                    "qty" => "2",
                    "sub_total" => 4,
                ],
                [
                    "item" => "ghudhiugi",
                    "unit_price" => "6",
                    "qty" => "3",
                    "sub_total" => 18,
                ],
            ],
            'provider_data' => [
                "hmo_code" => "HMO-A",
                "provider_name" => "Kingsley",
                "date" => "2021-11-18", // Encounter date
            ],
            'total' => 22,
        ];

        $this->json('post', '/api/create', $payload)
            ->assertStatus(200);

        $this->get(route('batch', ['HMO-A', 'Kingsley']))
            ->assertStatus(200);
    }
}
