<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpPageTest extends TestCase
{
    /**
     * Test that the help page loads successfully.
     */
    public function test_help_page_loads_successfully(): void
    {
        $response = $this->get('/help');

        $response->assertStatus(200);
        $response->assertViewIs('pages.help');
        $response->assertSee('Bantuan dan Dukungan');
        $response->assertSee('Panduan Kategori Laporan');
    }

    /**
     * Test that the help page contains priority categories.
     */
    public function test_help_page_contains_priority_categories(): void
    {
        $response = $this->get('/help');

        $response->assertStatus(200);
        $response->assertSee('Laporan Prioritas Tinggi');
        $response->assertSee('Laporan Prioritas Sedang');
        $response->assertSee('Laporan Prioritas Rendah');
        $response->assertSee('Keamanan dan Keselamatan Publik');
        $response->assertSee('Infrastruktur dan Fasilitas Umum');
        $response->assertSee('Administrasi dan Layanan Umum');
    }

    /**
     * Test that the help page contains specific examples.
     */
    public function test_help_page_contains_examples(): void
    {
        $response = $this->get('/help');

        $response->assertStatus(200);
        $response->assertSee('Pohon tumbang menutup jalan utama desa');
        $response->assertSee('Jalan desa berlubang, tapi masih bisa dilewati');
        $response->assertSee('Keterlambatan pelayanan surat keterangan domisili');
    }

    /**
     * Test that the help page contains emergency contacts.
     */
    public function test_help_page_contains_emergency_contacts(): void
    {
        $response = $this->get('/help');

        $response->assertStatus(200);
        $response->assertSee('Kontak Darurat');
        $response->assertSee('110'); // Police
        $response->assertSee('113'); // Fire Department
        $response->assertSee('118'); // Ambulance
        $response->assertSee('115'); // SAR
    }
}