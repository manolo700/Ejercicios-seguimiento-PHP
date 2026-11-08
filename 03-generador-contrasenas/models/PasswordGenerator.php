<?php
class PasswordGenerator {
    private const UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    private const NUMBERS = '0123456789';
    private const SYMBOLS = '!@#$%^&*()-_=+[]{}<>?~:;,.';
    private const AMBIGUOUS = ['0', 'O', 'o', '1', 'l', 'I'];

    public function generate(
        int $length = 16,
        bool $useUpper = true,
        bool $useLower = true,
        bool $useNumbers = true,
        bool $useSymbols = true,
        bool $excludeAmbiguous = false,
        int $quantity = 1
    ): array {
        $length = max(6, min(64, $length));
        $quantity = max(1, min(10, $quantity));

        $pools = [];
        if ($useUpper) $pools['upper'] = self::UPPERCASE;
        if ($useLower) $pools['lower'] = self::LOWERCASE;
        if ($useNumbers) $pools['numbers'] = self::NUMBERS;
        if ($useSymbols) $pools['symbols'] = self::SYMBOLS;

        // Fallback if none selected
        if (empty($pools)) {
            $pools['lower'] = self::LOWERCASE;
            $pools['numbers'] = self::NUMBERS;
            $useLower = true;
            $useNumbers = true;
        }

        if ($excludeAmbiguous) {
            foreach ($pools as $key => $chars) {
                $pools[$key] = str_replace(self::AMBIGUOUS, '', $chars);
            }
        }

        $allChars = implode('', $pools);
        $poolSize = strlen($allChars);

        $passwords = [];
        for ($q = 0; $q < $quantity; $q++) {
            $passChars = [];

            // Ensure at least one character from each selected pool
            foreach ($pools as $pool) {
                if (strlen($pool) > 0) {
                    $passChars[] = $pool[random_int(0, strlen($pool) - 1)];
                }
            }

            // Fill the rest
            while (count($passChars) < $length) {
                $passChars[] = $allChars[random_int(0, $poolSize - 1)];
            }

            // Shuffle deterministically with Fisher-Yates
            for ($i = count($passChars) - 1; $i > 0; $i--) {
                $j = random_int(0, $i);
                $tmp = $passChars[$i];
                $passChars[$i] = $passChars[$j];
                $passChars[$j] = $tmp;
            }

            $passwords[] = implode('', $passChars);
        }

        // Calculate entropy and strength
        $entropy = round($length * (log($poolSize) / log(2)), 1);
        $strength = $this->evaluateStrength($entropy, $length, count($pools));

        return [
            'passwords' => $passwords,
            'primary' => $passwords[0],
            'length' => $length,
            'pool_size' => $poolSize,
            'entropy' => $entropy,
            'strength' => $strength,
            'options' => [
                'upper' => $useUpper,
                'lower' => $useLower,
                'numbers' => $useNumbers,
                'symbols' => $useSymbols,
                'exclude_ambiguous' => $excludeAmbiguous,
                'quantity' => $quantity
            ]
        ];
    }

    private function evaluateStrength(float $entropy, int $length, int $poolCount): array {
        if ($entropy < 35 || $length < 8 || $poolCount < 2) {
            return [
                'level' => 'Débil',
                'color' => 'danger',
                'percent' => 25,
                'description' => 'Fácil de vulnerar mediante ataques de fuerza bruta.'
            ];
        } elseif ($entropy < 55) {
            return [
                'level' => 'Media',
                'color' => 'warning',
                'percent' => 50,
                'description' => 'Aceptable para cuentas básicas, pero mejorable.'
            ];
        } elseif ($entropy < 80) {
            return [
                'level' => 'Fuerte',
                'color' => 'primary',
                'percent' => 75,
                'description' => 'Muy resistente, adecuada para la mayoría de servicios.'
            ];
        } else {
            return [
                'level' => 'Excelente / Muy Fuerte',
                'color' => 'success',
                'percent' => 100,
                'description' => 'Prácticamente impenetrable con computación estándar.'
            ];
        }
    }
}
