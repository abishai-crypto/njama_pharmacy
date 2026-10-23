<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\UnitOfMeasure;

class CategoryAndUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'PKT', 'name' => 'Packet (Pakiti)'],
            ['code' => 'BTL', 'name' => 'Bottle (Chupa)'],
            ['code' => 'AMPL', 'name' => 'Ampoule (Ampule)'],
            ['code' => 'TUBE', 'name' => 'Tube (Mrija / Tube)'],
            ['code' => 'VIAL', 'name' => 'Vial (Chupa ya sindano)'],
            ['code' => 'PAC', 'name' => 'Pack (Kipande / Pakiti)'],
            ['code' => 'PCS', 'name' => 'Pieces (Vipande)'],
            ['code' => 'PAIR', 'name' => 'Pair (Jozi)'],
            ['code' => 'ROLL', 'name' => 'Roll (Mviringo)'],
            ['code' => 'BAR', 'name' => 'Bar (Kipande cha sabuni)'],
            ['code' => 'BOX', 'name' => 'Box (Sanduku)'],
            ['code' => 'JAR', 'name' => 'Jar (Kopo)'],
            ['code' => 'TABS', 'name' => 'Tablets (Vidonge)'],
            ['code' => 'CAPS', 'name' => 'Capsules (Kapsuli)'],
        ];

        foreach ($units as $u) {
            UnitOfMeasure::firstOrCreate(['code' => $u['code']], $u);
        }

        $categories = [
            ['name' => 'Antibiotics & Antimicrobials', 'slug' => 'antibiotics-antimicrobials', 'description' => 'Antibacterial, antifungal, antiviral medications'],
            ['name' => 'Antimalarials', 'slug' => 'antimalarials', 'description' => 'Malaria treatment and prophylaxis drugs'],
            ['name' => 'Analgesics & Anti-inflammatory (Painkillers)', 'slug' => 'painkillers-anti-inflammatory', 'description' => 'Pain relief and anti-inflammatory medications'],
            ['name' => 'Cough, Cold & Respiratory', 'slug' => 'cough-cold-respiratory', 'description' => 'Cough syrups, inhalers, bronchodilators, antihistamines'],
            ['name' => 'Gastrointestinal & Worms', 'slug' => 'gastrointestinal-worms', 'description' => 'Antacids, dewormers, antidiarrheals, laxatives'],
            ['name' => 'Cardiovascular & Antihypertensives', 'slug' => 'cardiovascular-antihypertensives', 'description' => 'Blood pressure and cardiovascular support medicines'],
            ['name' => 'Vitamins, Tonics & Supplements', 'slug' => 'vitamins-tonics-supplements', 'description' => 'Multivitamins, iron, calcium, dietary supplements'],
            ['name' => 'Dermatologicals & Topicals', 'slug' => 'dermatologicals-topicals', 'description' => 'Skin creams, antiseptics, lotions, ointments'],
            ['name' => 'Maternal & Reproductive Health', 'slug' => 'maternal-reproductive-health', 'description' => 'Contraceptives, pregnancy care, delivery kits, hormones'],
            ['name' => 'Medical Equipment & Diagnostic Kits', 'slug' => 'medical-equipment-diagnostic', 'description' => 'BP machines, glucose monitors, malaria rapid tests, pregnancy tests'],
            ['name' => 'Hospital & First Aid Supplies', 'slug' => 'hospital-first-aid-supplies', 'description' => 'Bandages, gauze rolls, POP, syringes, cannulas, gloves, cotton wool'],
            ['name' => 'Personal Care & Hygiene', 'slug' => 'personal-care-hygiene', 'description' => 'Baby and adult diapers, sanitary pads, antiseptic soaps'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(['slug' => $c['slug']], $c);
        }
    }
}
