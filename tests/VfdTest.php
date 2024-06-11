<?php

use Alphaolomi\SimplifyVfd\SimplifyVfd;

it('can instantiate the class', function () {
    expect(true)->toBeTrue();

    $vfd = new SimplifyVfd();

    expect($vfd)->toBeInstanceOf(SimplifyVfd::class);
});
