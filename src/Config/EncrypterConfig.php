<?php

declare(strict_types=1);

namespace Chiron\Encrypter\Config;

use Chiron\Config\AbstractInjectableConfig;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

// TODO : il faudra utiliser la clés qui est stockée dans APP_KEY et surtout utiliser la fonction hex2bin pour décoder cette chaine de caractére et l'utiliser comme une clés de bytes. Il faudra donc vérifier que la clés de byte fait bien 32 bytes une fois décodée via hex2bin et surtout pour utiliser hex2bin il faut vérifier que la chaine est bien de type hexa et que la longueur est un multiple de 2 (cad "even") [il faudrait même vérifier si la taille === 64 chars car c'est l'équivalent de 32 bytes] car sinon on aura un warning dans la méthode hex2bin et elle retournera false au lien de décoder la chaine.
//=> https://stackoverflow.com/questions/41194159/how-to-catch-hex2bin-warning


// TODO : créer une méthode getRawKey() qui se charge de faire un hex2bin pour avoir un résultat sous forme de 32 bytes ?

final class EncrypterConfig extends AbstractInjectableConfig
{
    protected const CONFIG_SECTION_NAME = 'encrypter';

    protected function getConfigSchema(): Schema
    {
        return Expect::structure([
            'key' => Expect::string()->default(env('APP_KEY')),
        ]);
    }

    public function getKey(): string
    {
        return $this->get('key');
    }
}
