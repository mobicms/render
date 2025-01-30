<?php

declare(strict_types=1);

namespace MobicmsTest\Render;

use InvalidArgumentException;
use Mobicms\Render\Engine;
use MobicmsTest\Stubs\FakeExtension;

$engine = new Engine();

test('The default template file extension should be "phtml"', function () use ($engine) {
    expect($engine->getFileExtension())->toBe('phtml');
});

test('Let\'s try to add and then get several folders', function () use ($engine) {
    $engine->addPath('folder1', 'ns1')
        ->addPath('folder2', 'ns1')
        ->addPath(M_PATH_ROOT);
    expect($engine->getPath('ns1'))->toContain('folder1')
        ->and($engine->getPath('ns1'))->toContain('folder2')
        ->and($engine->getPath('main'))->toContain(rtrim(M_PATH_ROOT, '/\\'));
});

test('Ability to pass data to a template', function () use ($engine) {
    // Share data with all templates
    $engine->addData(['all' => 'AllTemplatesData']);

    // Passing data to a specific template
    $engine->addData(['tpl1' => 'Tpl1Data'], ['template1']);
    $engine->addData(['tpl2' => 'Tpl2Data'], ['template2']);

    expect($engine->getTemplateData()['all'])->toBe('AllTemplatesData')
        ->and($engine->getTemplateData('template1')['tpl1'])->toBe('Tpl1Data')
        ->and($engine->getTemplateData('template2')['tpl2'])->toBe('Tpl2Data');
});

test('Can render template', function () use ($engine) {
    $engine->addPath(M_PATH_ROOT);
    expect($engine->render('main::tpl-data', ['var' => 'Hello!']))->toBe('Hello!');
});

test('Ability to register your own functions', function () use ($engine) {
    $engine->registerFunction('uppercase', 'strtoupper');
    $engine->addPath(M_PATH_ROOT);
    $result = $engine->render(
        'main::tpl-func-uppercase',
        ['var' => 'abcdefgh']
    );
    expect($result)->toBe('ABCDEFGH');
});

test('Possibility to request a registered function', function () use ($engine) {
    $engine->registerFunction('lowercase', 'strtolower');
    $function = $engine->getFunction('lowercase');
    expect($function('TTT'))->toBe('ttt');
});

test('Ability to check whether a function is registered', function () use ($engine) {
    expect($engine->doesFunctionExist('uppercase'))->toBeTrue()
        ->and($engine->doesFunctionExist('function_does_not_exist'))->toBeFalse();
});

test('Ability to load extensions', function () use ($engine) {
    expect($engine->doesFunctionExist('foo'))->toBeFalse();
    $engine->loadExtension(new FakeExtension());
    expect($engine->doesFunctionExist('foo'))->toBeTrue();
});

describe('Exception handling:', function () {
    $engine = new Engine();

    test('adding a folder with an empty namespace', function () use ($engine) {
        $engine->addPath('folder', '');
    })->throws(InvalidArgumentException::class, 'Namespace cannot be empty.');

    test('adding a folder with an empty path', function () use ($engine) {
        $engine->addPath('');
    })->throws(InvalidArgumentException::class, 'You must specify folder.');

    test('accessing a nonexistent namespace', function () use ($engine) {
        $engine->getPath('name');
    })->throws(InvalidArgumentException::class, 'The template namespace "name" was not found.');

    test('trying to register an existing function', function () use ($engine) {
        $engine->registerFunction('uppercase', 'strtoupper');
        $engine->registerFunction('uppercase', 'strtoupper');
    })->throws(InvalidArgumentException::class, 'The template function name "uppercase" is already registered.');

    test('trying to register a function with an incorrect name', function () use ($engine) {
        $engine->registerFunction('invalid name', 'strtoupper');
    })->throws(InvalidArgumentException::class);

    test('trying to request a non-existent function', function () use ($engine) {
        $engine->getFunction('function_does_not_exist');
    })->throws(InvalidArgumentException::class, 'The template function "function_does_not_exist" was not found.');
});
