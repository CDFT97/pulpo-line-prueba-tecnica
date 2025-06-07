<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $freeUser;
    protected $premiumUser;
    protected $freeRole;
    protected $premiumRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles
        $this->freeRole = Role::factory()->create(['name' => 'free', 'id' => 1]);
        $this->premiumRole = Role::factory()->create(['name' => 'premium', 'id' => 2]);

        // Crear usuarios
        $this->freeUser = User::factory()->create(['role_id' => $this->freeRole->id]);
        $this->premiumUser = User::factory()->create(['role_id' => $this->premiumRole->id]);
    }

    #[Test]
    public function upgrade_to_premium_requires_authentication()
    {
        $response = $this->getJson('/api/upgrade-to-premium');

        $response->assertStatus(401);
    }

    #[Test]
    public function free_user_can_upgrade_to_premium()
    {
        // 1. Preparación - Crear usuario con rol free
        $freeRole = Role::where(['name' => 'free'])->first();
        $premiumRole = Role::where(['name' => 'premium'])->first();
        $user = User::factory()->create(['role_id' => $freeRole->id]);

        // 2. Ejecución - Llamar al endpoint
        $response = $this->actingAs($user)
            ->getJson('/api/upgrade-to-premium');

        // 3. Verificaciones
        $response->assertStatus(200);

        // Verificar que el rol cambió en la base de datos
        $user->refresh(); // Actualizar modelo con datos de la BD
        $this->assertEquals(
            $premiumRole->id,
            $user->role_id,
            'El ID del rol del usuario debería ser el de premium'
        );

        // Verificar que el nombre del rol es premium
        $this->assertEquals(
            'premium',
            $user->role->name,
            'El nombre del rol debería ser "premium"'
        );
    }

    #[Test]
    public function premium_user_cannot_upgrade_again()
    {
        $response = $this->actingAs($this->premiumUser)
            ->getJson('/api/upgrade-to-premium');

        $response->assertStatus(403);
    }

    #[Test]
    public function upgrade_fails_when_premium_role_not_found()
    {
        // Crear un usuario con un rol que no es premium
        $user = User::factory()->create(['role_id' => $this->freeRole->id]);

        // Eliminar el rol premium (no debería haber usuarios con este rol)
        $this->premiumRole->delete();

        $response = $this->actingAs($user)
            ->getJson('/api/upgrade-to-premium');

        $response->assertStatus(500);
    }
}
