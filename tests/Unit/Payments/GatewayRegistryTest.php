<?php

namespace Tests\Unit\Payments;

use App\Services\Payments\DTOs\GatewayDefinition;
use App\Services\Payments\DTOs\FieldDefinition;
use App\Services\Payments\PaymentGatewayRegistry;
use Database\Seeders\PaymentGatewaySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GatewayRegistryTest extends TestCase
{
    use RefreshDatabase;

    private PaymentGatewayRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PaymentGatewaySeeder::class);
        $this->registry = $this->app->make(PaymentGatewayRegistry::class);
    }

    public function test_all_gateways_defined(): void
    {
        $expected = ['chargily', 'baridimob', 'paypal', 'redotpay', 'stripe', 'wise', 'wise_manual', 'payoneer', 'cash', 'delivery', 'noest'];
        $all = $this->registry->all();
        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $all, "Gateway '$key' must be defined in registry");
        }
    }

    public function test_each_definition_has_required_properties(): void
    {
        foreach ($this->registry->all() as $key => $def) {
            $this->assertInstanceOf(GatewayDefinition::class, $def);
            $this->assertNotEmpty($def->name, "Gateway '$key' must have a name");
            $this->assertNotEmpty($def->key, "Gateway '$key' must have a key");
            $this->assertIsArray($def->fields, "Gateway '$key' fields must be an array");
            $this->assertNotEmpty($def->fields, "Gateway '$key' must have at least one field");
        }
    }

    public function test_each_field_has_required_properties(): void
    {
        foreach ($this->registry->all() as $key => $def) {
            foreach ($def->fields as $field) {
                $this->assertInstanceOf(FieldDefinition::class, $field);
                $this->assertNotEmpty($field->key, "Field in gateway '$key' must have a key");
                $this->assertNotEmpty($field->label, "Field {$field->key} in gateway '$key' must have a label");
                $this->assertContains($field->type, ['text', 'password', 'select', 'boolean', 'textarea', 'url', 'email'], "Field {$field->key} in '$key' has invalid type: {$field->type}");
            }
        }
    }

    public function test_find_returns_definition(): void
    {
        $chargily = $this->registry->find('chargily');
        $this->assertNotNull($chargily);
        $this->assertInstanceOf(GatewayDefinition::class, $chargily);
        $this->assertSame('chargily', $chargily->key);
    }

    public function test_find_returns_null_for_unknown(): void
    {
        $this->assertNull($this->registry->find('nonexistent'));
    }

    public function test_categories_returns_array(): void
    {
        $categories = $this->registry->categories();
        $this->assertIsArray($categories);
        $this->assertNotEmpty($categories);
    }

    public function test_field_validation_rules_are_valid(): void
    {
        foreach ($this->registry->all() as $key => $def) {
            foreach ($def->fields as $field) {
                $rules = $field->validationRules();
                $this->assertIsArray($rules, "Field {$field->key} in '$key' must return array rules");
                foreach ($rules as $rule) {
                    $this->assertIsString($rule, "Rule for {$field->key} in '$key' must be string: got $rule");
                }
                if ($field->required) {
                    $this->assertContains('required', $rules, "Field {$field->key} in '$key' is required but missing required rule");
                }
            }
        }
    }

    public function test_field_without_encrypted_has_no_encrypted_flag(): void
    {
        foreach ($this->registry->all() as $key => $def) {
            foreach ($def->fields as $field) {
                $this->assertIsBool($field->encrypted, "Field {$field->key} in '$key' encrypted must be bool");
            }
        }
    }
}
