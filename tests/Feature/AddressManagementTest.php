<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_new_address()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('addresses.store'), [
            'full_name'     => 'John Doe',
            'phone'         => '9876543210',
            'address_line1' => '123 Main Street',
            'address_line2' => 'Apt 4B',
            'city'          => 'Mumbai',
            'state'         => 'Maharashtra',
            'pincode'       => '400001',
            'label'         => 'Home',
            'is_default'    => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_addresses', [
            'user_id'   => $user->id,
            'full_name' => 'John Doe',
            'city'      => 'Mumbai',
            'pincode'   => '400001',
        ]);
    }

    public function test_user_can_delete_own_address()
    {
        $user = User::factory()->create();
        $address = UserAddress::create([
            'user_id'       => $user->id,
            'full_name'     => 'Jane Doe',
            'phone'         => '9876543210',
            'address_line1' => '456 Park Avenue',
            'city'          => 'Delhi',
            'state'         => 'Delhi',
            'pincode'       => '110001',
            'label'         => 'Work',
            'is_default'    => true,
        ]);

        $response = $this->actingAs($user)->delete(route('addresses.destroy', $address->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('user_addresses', [
            'id' => $address->id,
        ]);
    }
}
