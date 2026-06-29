<?php

it('renders the about page', function () {
    $response = $this->get('/about');

    $response->assertStatus(200);
    $response->assertSee('About Us');
});
