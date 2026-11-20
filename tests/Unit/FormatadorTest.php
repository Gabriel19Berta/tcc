<?php

use App\Support\Formatador;
use Carbon\Carbon;

describe('moeda e reais', function () {
    test('formata no padrão brasileiro', function () {
        expect(Formatador::moeda(1234.56))->toBe('1.234,56')
            ->and(Formatador::reais(1234.56))->toBe('R$ 1.234,56')
            ->and(Formatador::reais(0.1 + 0.2))->toBe('R$ 0,30');
    });

    test('aceita decimal vindo do banco como texto', function () {
        expect(Formatador::reais('1234567.8'))->toBe('R$ 1.234.567,80');
    });

    test('nulo vira zero', function () {
        expect(Formatador::moeda(null))->toBe('0,00')
            ->and(Formatador::reais(null))->toBe('R$ 0,00');
    });
});

describe('cpf', function () {
    test('formata 11 dígitos, com ou sem máscara', function () {
        expect(Formatador::cpf('12345678900'))->toBe('123.456.789-00')
            ->and(Formatador::cpf('123.456.789-00'))->toBe('123.456.789-00');
    });

    test('vazio retorna string vazia', function () {
        expect(Formatador::cpf(null))->toBe('')
            ->and(Formatador::cpf(''))->toBe('')
            ->and(Formatador::cpf('   '))->toBe('');
    });

    test('quantidade inesperada devolve o valor original', function () {
        expect(Formatador::cpf('123456'))->toBe('123456')
            ->and(Formatador::cpf('123456789001'))->toBe('123456789001');
    });
});

describe('cnpj', function () {
    test('formata CNPJ numérico', function () {
        expect(Formatador::cnpj('12345678000195'))->toBe('12.345.678/0001-95')
            ->and(Formatador::cnpj('12.345.678/0001-95'))->toBe('12.345.678/0001-95');
    });

    test('formata CNPJ alfanumérico preservando as letras', function () {
        expect(Formatador::cnpj('12ABC34501DE35'))->toBe('12.ABC.345/01DE-35')
            ->and(Formatador::cnpj('12.ABC.345/01DE-35'))->toBe('12.ABC.345/01DE-35')
            ->and(Formatador::cnpj('12abc34501de35'))->toBe('12.ABC.345/01DE-35');
    });

    test('vazio retorna string vazia', function () {
        expect(Formatador::cnpj(null))->toBe('')
            ->and(Formatador::cnpj(''))->toBe('');
    });

    test('formato inesperado devolve o valor original sem remover letras', function () {
        expect(Formatador::cnpj('12ABC345'))->toBe('12ABC345')
            ->and(Formatador::cnpj('12ABC34501DEXY'))->toBe('12ABC34501DEXY'); // dígitos verificadores devem ser números
    });
});

describe('telefone, celular e cep', function () {
    test('formata quando a quantidade de dígitos confere', function () {
        expect(Formatador::telefone('4433334444'))->toBe('(44) 3333-4444')
            ->and(Formatador::telefone('(44) 3333-4444'))->toBe('(44) 3333-4444')
            ->and(Formatador::celular('44999998888'))->toBe('(44) 99999-8888')
            ->and(Formatador::celular('(44) 99999-8888'))->toBe('(44) 99999-8888')
            ->and(Formatador::cep('87000000'))->toBe('87000-000');
    });

    test('não troca telefone por celular nem completa números', function () {
        expect(Formatador::telefone('44999998888'))->toBe('44999998888')
            ->and(Formatador::celular('4433334444'))->toBe('4433334444')
            ->and(Formatador::celular('449999'))->toBe('449999');
    });

    test('vazio retorna string vazia', function () {
        expect(Formatador::telefone(null))->toBe('')
            ->and(Formatador::celular(''))->toBe('')
            ->and(Formatador::cep(null))->toBe('');
    });
});

describe('data', function () {
    test('mantém o formato padrão usado nos relatórios', function () {
        expect(Formatador::data(Carbon::create(2026, 11, 20, 14, 30, 5)))->toBe('20/11/2026 14:30:05');
    });

    test('aceita formato personalizado, DateTime nativo e texto', function () {
        expect(Formatador::data(Carbon::create(2026, 11, 20, 14, 30), 'd/m/Y H:i'))->toBe('20/11/2026 14:30')
            ->and(Formatador::data(new DateTimeImmutable('2026-01-05'), 'd/m/Y'))->toBe('05/01/2026')
            ->and(Formatador::data('2026-03-15', 'd/m/Y'))->toBe('15/03/2026');
    });

    test('vazio retorna string vazia', function () {
        expect(Formatador::data(null))->toBe('')
            ->and(Formatador::data(''))->toBe('');
    });
});

test('status booleano', function () {
    expect(Formatador::status(true))->toBe('Ativo')
        ->and(Formatador::status(false))->toBe('Inativo');
});
