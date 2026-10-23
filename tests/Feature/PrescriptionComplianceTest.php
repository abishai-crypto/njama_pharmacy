<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Prescription;

class PrescriptionComplianceTest extends TestCase
{
    public function test_pharmacist_can_record_prescription_with_items(): void
    {
        $pharmacist = User::where('username', 'pharmacist')->first();
        $product = Product::first();

        $rxNum = 'RX-TEST-' . time();
        $payload = [
            'prescription_number' => $rxNum,
            'prescription_date' => date('Y-m-d'),
            'patient_name' => 'Amina Juma',
            'patient_age' => 34,
            'patient_gender' => 'female',
            'patient_phone' => '0712345678',
            'prescriber_name' => 'Dr. K. Rashid',
            'prescriber_hospital' => 'Amana Regional Referral Hospital',
            'prescriber_phone' => '0754112233',
            'prescriber_reg_no' => 'MCT-99882',
            'diagnosis_summary' => 'Severe Respiratory Tract Infection',
            'notes' => 'Patient has mild penicillin allergy; monitor closely.',
            'items' => [
                [
                    'product_id' => $product->id,
                    'dosage_instructions' => '1x3 kwa siku 7',
                    'prescribed_quantity' => 21,
                    'duration_days' => 7,
                ],
            ],
        ];

        $response = $this->actingAs($pharmacist)->post('/prescriptions', $payload);

        $response->assertStatus(302);
        $response->assertRedirect('/prescriptions');

        $rx = Prescription::where('prescription_number', $rxNum)->with('items')->first();
        $this->assertNotNull($rx);
        $this->assertEquals('Amina Juma', $rx->patient_name);
        $this->assertEquals('Dr. K. Rashid', $rx->prescriber_name);
        $this->assertCount(1, $rx->items);
        $this->assertEquals(21, $rx->items[0]->prescribed_quantity);

        // View prescription show page
        $showResponse = $this->actingAs($pharmacist)->get("/prescriptions/{$rx->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Amina Juma');
        $showResponse->assertSee('Dr. K. Rashid');
    }
}
