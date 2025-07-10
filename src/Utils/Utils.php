<?php

namespace NFSe\Utils;

class Utils
{
    public static function limparCnpjCpf(string $documento): string
    {
        return preg_replace('/\D/', '', $documento);
    }

    public static function formatarCnpj(string $cnpj): string
    {
        $cnpj = self::limparCnpjCpf($cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }

    public static function formatarCpf(string $cpf): string
    {
        $cpf = self::limparCnpjCpf($cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public static function validarCnpj(string $cnpj): bool
    {
        $cnpj = self::limparCnpjCpf($cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    public static function validarCpf(string $cpf): bool
    {
        $cpf = self::limparCnpjCpf($cpf);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    public static function limparCep(string $cep): string
    {
        return preg_replace('/\D/', '', $cep);
    }

    public static function formatarCep(string $cep): string
    {
        $cep = self::limparCep($cep);
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
    }

    public static function validarCep(string $cep): bool
    {
        $cep = self::limparCep($cep);
        return preg_match('/^\d{8}$/', $cep);
    }

    public static function validarEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function limparXml(string $xml): string
    {
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $xml);
    }

    public static function formatarValor(float $valor, int $decimais = 2): string
    {
        return number_format($valor, $decimais, '.', '');
    }

    public static function obterCodigoMunicipio(string $municipio, string $uf): string
    {
        // Tabela básica de códigos IBGE - pode ser expandida
        $municipios = [
            'fortaleza-ce' => '2304400',
            'sao_paulo-sp' => '3550308',
            'rio_de_janeiro-rj' => '3304557',
            'belo_horizonte-mg' => '3106200',
            'brasilia-df' => '5300108',
        ];

        $chave = strtolower($municipio . '-' . $uf);

        return $municipios[$chave] ?? '';
    }

    public static function gerarNumeroRps(): string
    {
        return Utils . phpdate('YmdHis') . mt_rand(100, 999);
    }
}