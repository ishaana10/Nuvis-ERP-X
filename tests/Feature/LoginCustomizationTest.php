<?php

it('renders NUVIS ERP X below logo and version v1.0 at bottom of login page', function () {
    $response = $this->get(route('filament.admin.auth.login'));

    $response->assertOk();
    $response->assertSee('NUVIS ERP X');
    $response->assertSee('v1.0');
});
