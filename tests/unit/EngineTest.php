<?php

declare(strict_types=1);

namespace MobicmsTest\Render;

use InvalidArgumentException;
use Mobicms\Render\Engine;

test('The default template file extension should be "phtml"', function () {
    $engine = new Engine();
    expect($engine->getFileExtension())->toBe('phtml');
});

test('Let\'s try to add and then get several folders', function () {
    $engine = new Engine();
    $engine->addPath('folder1', 'ns1')
        ->addPath('folder2', 'ns1')
        ->addPath(M_PATH_ROOT);
    expect($engine->getPath('ns1'))->toContain('folder1')
        ->and($engine->getPath('ns1'))->toContain('folder2')
        ->and($engine->getPath('main'))->toContain(rtrim(M_PATH_ROOT, '/\\'));
});

test('Adding a folder with an empty namespace throws an exception', function () {
    $engine = new Engine();
    $engine->addPath('folder', '');
})->throws(InvalidArgumentException::class, 'Namespace cannot be empty.');

test('Adding a folder with an empty path throws an exception', function () {
    $engine = new Engine();
    $engine->addPath('');
})->throws(InvalidArgumentException::class, 'You must specify folder.');

test('Accessing a nonexistent namespace throws an exception', function () {
    $engine = new Engine();
    $engine->getPath('name');
})->throws(InvalidArgumentException::class, 'The template namespace "name" was not found.');

test('Ability to pass data to a template', function () {
    $engine = new Engine();

    // Share data with all templates
    $engine->addData(['all' => 'AllTemplatesData']);

    // Passing data to a specific template
    $engine->addData(['tpl1' => 'Tpl1Data'], ['template1']);
    $engine->addData(['tpl2' => 'Tpl2Data'], ['template2']);

    expect($engine->getTemplateData()['all'])->toBe('AllTemplatesData')
        ->and($engine->getTemplateData('template1')['tpl1'])->toBe('Tpl1Data')
        ->and($engine->getTemplateData('template2')['tpl2'])->toBe('Tpl2Data');
});

test('Can render template', function () {
    $engine = new Engine();
    $engine->addPath(M_PATH_ROOT);
    expect($engine->render('main::tpl-data', ['var' => 'Hello!']))->toBe('Hello!');
});

test('Ability to register your own functions', function () {
    $engine = new Engine();
    $engine->registerFunction('uppercase', 'strtoupper');
    $engine->addPath(M_PATH_ROOT);
    $result = $engine->render(
        'main::tpl-func-uppercase',
        ['var' => 'abcdefgh']
    );
    expect($result)->toBe('ABCDEFGH');
});

test('Trying to register an existing function throws an exception', function () {
    $engine = new Engine();
    $engine->registerFunction('uppercase', 'strtoupper');
    $engine->registerFunction('uppercase', 'strtoupper');
})->throws(InvalidArgumentException::class, 'The template function name "uppercase" is already registered.');

test('Trying to register a function with an incorrect name throws an exception', function () {
    $engine = new Engine();
    $engine->registerFunction('invalid name', 'strtoupper');
})->throws(InvalidArgumentException::class);

test('Possibility to request a registered function', function () {
    $engine = new Engine();
    $engine->registerFunction('uppercase', 'strtoupper');
    $function = $engine->getFunction('uppercase');
    expect($function('ttt'))->toBe('TTT');
});

test('Trying to request a non-existent function throws an exception', function () {
    $engine = new Engine();
    $engine->getFunction('function_does_not_exist');
})->throws(InvalidArgumentException::class, 'The template function "function_does_not_exist" was not found.');

test('Ability to check whether a function is registered', function () {
    $engine = new Engine();
    $engine->registerFunction('uppercase', 'strtoupper');
    expect($engine->doesFunctionExist('uppercase'))->toBeTrue()
        ->and($engine->doesFunctionExist('function_does_not_exist'))->toBeFalse();
});

test('Ability to load extensions', function () {
    $engine = new Engine();
    expect($engine->doesFunctionExist('foo'))->toBeFalse();
    $engine->loadExtension(new FakeExtension());
    expect($engine->doesFunctionExist('foo'))->toBeTrue();
});
