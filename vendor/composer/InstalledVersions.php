<?php











namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;








class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => 'dev-master',
    'version' => 'dev-master',
    'aliases' => 
    array (
    ),
    'reference' => 'f9ebaece76cab81451bc02baa74e8b52ed5dd931',
    'name' => 'soft/org',
  ),
  'versions' => 
  array (
    'arvenil/ninja-mutex' => 
    array (
      'pretty_version' => '0.6.0',
      'version' => '0.6.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '8c0f5d10f8fb1481b9d98488dbbe2bf84c2e1a25',
    ),
    'chumper/zipper' => 
    array (
      'pretty_version' => 'v1.0.3',
      'version' => '1.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd15207e010f8fe1bdd341376bd86d599c4166423',
    ),
    'classpreloader/classpreloader' => 
    array (
      'pretty_version' => '3.2.1',
      'version' => '3.2.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '297db07cabece3946f4a98d23f11f90aa10e1797',
    ),
    'colinodell/commonmark-php' => 
    array (
      'replaced' => 
      array (
        0 => '*',
      ),
    ),
    'danielstjules/stringy' => 
    array (
      'pretty_version' => '1.10.0',
      'version' => '1.10.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '4749c205db47ee5b32e8d1adf6d9aff8db6caf3b',
    ),
    'dnoegel/php-xdg-base-dir' => 
    array (
      'pretty_version' => '0.1',
      'version' => '0.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '265b8593498b997dc2d31e75b89f053b5cc9621a',
    ),
    'doctrine/annotations' => 
    array (
      'pretty_version' => 'v1.4.0',
      'version' => '1.4.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '54cacc9b81758b14e3ce750f205a393d52339e97',
    ),
    'doctrine/cache' => 
    array (
      'pretty_version' => 'v1.6.2',
      'version' => '1.6.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'eb152c5100571c7a45470ff2a35095ab3f3b900b',
    ),
    'doctrine/collections' => 
    array (
      'pretty_version' => 'v1.4.0',
      'version' => '1.4.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '1a4fb7e902202c33cce8c55989b945612943c2ba',
    ),
    'doctrine/common' => 
    array (
      'pretty_version' => 'v2.7.3',
      'version' => '2.7.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '4acb8f89626baafede6ee5475bc5844096eba8a9',
    ),
    'doctrine/dbal' => 
    array (
      'pretty_version' => 'v2.5.13',
      'version' => '2.5.13.0',
      'aliases' => 
      array (
      ),
      'reference' => '729340d8d1eec8f01bff708e12e449a3415af873',
    ),
    'doctrine/inflector' => 
    array (
      'pretty_version' => 'v1.1.0',
      'version' => '1.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '90b2128806bfde671b6952ab8bea493942c1fdae',
    ),
    'doctrine/lexer' => 
    array (
      'pretty_version' => '1.0.2',
      'version' => '1.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '1febd6c3ef84253d7c815bed85fc622ad207a9f8',
    ),
    'easywechat-composer/easywechat-composer' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '9999999-dev',
      ),
      'reference' => '4d6acc9baa9f02cd80c007f14e22f7e5d6931409',
    ),
    'elasticsearch/elasticsearch' => 
    array (
      'pretty_version' => 'v5.5.0',
      'version' => '5.5.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '48b8a90e2b97b4d69ce42851c1b9e59f8054661a',
    ),
    'ezyang/htmlpurifier' => 
    array (
      'pretty_version' => 'v4.9.3',
      'version' => '4.9.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '95e1bae3182efc0f3422896a3236e991049dac69',
    ),
    'guzzlehttp/guzzle' => 
    array (
      'pretty_version' => '6.5.5',
      'version' => '6.5.5.0',
      'aliases' => 
      array (
      ),
      'reference' => '9d4290de1cfd701f38099ef7e183b64b4b7b0c5e',
    ),
    'guzzlehttp/promises' => 
    array (
      'pretty_version' => '1.4.1',
      'version' => '1.4.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '8e7d04f1f6450fef59366c399cfad4b9383aa30d',
    ),
    'guzzlehttp/psr7' => 
    array (
      'pretty_version' => '1.8.2',
      'version' => '1.8.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'dc960a912984efb74d0a90222870c72c87f10c91',
    ),
    'guzzlehttp/ringphp' => 
    array (
      'pretty_version' => '1.1.1',
      'version' => '1.1.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '5e2a174052995663dd68e6b5ad838afd47dd615b',
    ),
    'guzzlehttp/streams' => 
    array (
      'pretty_version' => '3.0.0',
      'version' => '3.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '47aaa48e27dae43d39fc1cea0ccf0d84ac1a2ba5',
    ),
    'illuminate/auth' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/broadcasting' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/bus' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/cache' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/config' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/console' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/container' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/contracts' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/cookie' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/database' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/encryption' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/events' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/exception' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/filesystem' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/hashing' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/http' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/log' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/mail' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/pagination' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/pipeline' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/queue' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/redis' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/routing' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/session' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/support' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/translation' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/validation' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'illuminate/view' => 
    array (
      'replaced' => 
      array (
        0 => 'v5.1.46',
      ),
    ),
    'intervention/image' => 
    array (
      'pretty_version' => '2.5.1',
      'version' => '2.5.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'abbf18d5ab8367f96b3205ca3c89fb2fa598c69e',
    ),
    'jakub-onderka/php-console-color' => 
    array (
      'pretty_version' => 'v0.2',
      'version' => '0.2.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd5deaecff52a0d61ccb613bb3804088da0307191',
    ),
    'jakub-onderka/php-console-highlighter' => 
    array (
      'pretty_version' => 'v0.3.2',
      'version' => '0.3.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '7daa75df45242c8d5b75a22c00a201e7954e4fb5',
    ),
    'jaybizzle/crawler-detect' => 
    array (
      'pretty_version' => 'v1.2.106',
      'version' => '1.2.106.0',
      'aliases' => 
      array (
      ),
      'reference' => '78bf6792cbf9c569dc0bf2465481978fd2ed0de9',
    ),
    'jenssegers/agent' => 
    array (
      'pretty_version' => 'v2.6.4',
      'version' => '2.6.4.0',
      'aliases' => 
      array (
      ),
      'reference' => 'daa11c43729510b3700bc34d414664966b03bffe',
    ),
    'jeremeamia/superclosure' => 
    array (
      'pretty_version' => '2.4.0',
      'version' => '2.4.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '5707d5821b30b9a07acfb4d76949784aaa0e9ce9',
    ),
    'kylekatarnls/update-helper' => 
    array (
      'pretty_version' => '1.2.1',
      'version' => '1.2.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '429be50660ed8a196e0798e5939760f168ec8ce9',
    ),
    'laravel/framework' => 
    array (
      'pretty_version' => 'v5.1.46',
      'version' => '5.1.46.0',
      'aliases' => 
      array (
      ),
      'reference' => '7f2f892e62163138121e8210b92b21394fda8d1c',
    ),
    'league/commonmark' => 
    array (
      'pretty_version' => '0.18.5',
      'version' => '0.18.5.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f94e18d68260f43a7d846279cad88405854b1306',
    ),
    'league/commonmark-ext-table' => 
    array (
      'pretty_version' => '0.9.0',
      'version' => '0.9.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '94bc98d802d0b706e748716854e5fa0bd3644df3',
    ),
    'league/flysystem' => 
    array (
      'pretty_version' => '1.0.70',
      'version' => '1.0.70.0',
      'aliases' => 
      array (
      ),
      'reference' => '585824702f534f8d3cf7fab7225e8466cc4b7493',
    ),
    'maatwebsite/excel' => 
    array (
      'pretty_version' => '2.1.30',
      'version' => '2.1.30.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f5540c4ba3ac50cebd98b09ca42e61f926ef299f',
    ),
    'mews/captcha' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '9999999-dev',
      ),
      'reference' => '97c57de881a2e6a3e7d7747570f90dd042c5337d',
    ),
    'mews/purifier' => 
    array (
      'pretty_version' => 'v2.0.8',
      'version' => '2.0.8.0',
      'aliases' => 
      array (
      ),
      'reference' => '2a81746eb933c0c4338786f99a9cf66f24c02a6a',
    ),
    'mobiledetect/mobiledetectlib' => 
    array (
      'pretty_version' => '2.8.37',
      'version' => '2.8.37.0',
      'aliases' => 
      array (
      ),
      'reference' => '9841e3c46f5bd0739b53aed8ac677fa712943df7',
    ),
    'modstart/modstart' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '9999999-dev',
      ),
      'reference' => '40273f4d1978d54ceb4937fc39280b89a3e88535',
    ),
    'monolog/monolog' => 
    array (
      'pretty_version' => '1.26.1',
      'version' => '1.26.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'c6b00f05152ae2c9b04a448f99c7590beb6042f5',
    ),
    'mtdowling/cron-expression' => 
    array (
      'pretty_version' => 'v1.2.3',
      'version' => '1.2.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '9be552eebcc1ceec9776378f7dcc085246cacca6',
    ),
    'nesbot/carbon' => 
    array (
      'pretty_version' => '1.39.1',
      'version' => '1.39.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '4be0c005164249208ce1b5ca633cd57bdd42ff33',
    ),
    'nikic/php-parser' => 
    array (
      'pretty_version' => 'v2.1.1',
      'version' => '2.1.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '4dd659edadffdc2143e4753df655d866dbfeedf0',
    ),
    'paragonie/random_compat' => 
    array (
      'pretty_version' => 'v1.4.3',
      'version' => '1.4.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '9b3899e3c3ddde89016f576edb8c489708ad64cd',
    ),
    'phpoffice/phpexcel' => 
    array (
      'pretty_version' => '1.8.2',
      'version' => '1.8.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '1441011fb7ecdd8cc689878f54f8b58a6805f870',
    ),
    'pimple/pimple' => 
    array (
      'pretty_version' => 'v3.2.3',
      'version' => '3.2.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '9e403941ef9d65d20cba7d54e29fe906db42cf32',
    ),
    'predis/predis' => 
    array (
      'pretty_version' => 'v1.1.7',
      'version' => '1.1.7.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b240daa106d4e02f0c5b7079b41e31ddf66fddf8',
    ),
    'psr/container' => 
    array (
      'pretty_version' => '1.0.0',
      'version' => '1.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
    ),
    'psr/http-message' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f6561bf28d520154e4b0ec72be95418abe6d9363',
    ),
    'psr/http-message-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/log' => 
    array (
      'pretty_version' => '1.1.4',
      'version' => '1.1.4.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd49695b909c3b7628b6289db5479a1c204601f11',
    ),
    'psr/log-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0.0',
      ),
    ),
    'psr/simple-cache' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '408d5eafb83c57f6365a3ca330ff23aa4a5fa39b',
    ),
    'psy/psysh' => 
    array (
      'pretty_version' => 'v0.7.2',
      'version' => '0.7.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'e64e10b20f8d229cac76399e1f3edddb57a0f280',
    ),
    'ralouphie/getallheaders' => 
    array (
      'pretty_version' => '3.0.3',
      'version' => '3.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '120b605dfeb996808c31b6477290a714d356e822',
    ),
    'react/promise' => 
    array (
      'pretty_version' => 'v2.8.0',
      'version' => '2.8.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f3cff96a19736714524ca0dd1d4130de73dbbbc4',
    ),
    'soft/org' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
      ),
      'reference' => 'f9ebaece76cab81451bc02baa74e8b52ed5dd931',
    ),
    'swiftmailer/swiftmailer' => 
    array (
      'pretty_version' => 'v5.4.12',
      'version' => '5.4.12.0',
      'aliases' => 
      array (
      ),
      'reference' => '181b89f18a90f8925ef805f950d47a7190e9b950',
    ),
    'symfony/console' => 
    array (
      'pretty_version' => 'v2.7.51',
      'version' => '2.7.51.0',
      'aliases' => 
      array (
      ),
      'reference' => '574cb4cfaa01ba115fc2fc0c2355b2c5472a4804',
    ),
    'symfony/css-selector' => 
    array (
      'pretty_version' => 'v2.8.52',
      'version' => '2.8.52.0',
      'aliases' => 
      array (
      ),
      'reference' => '7b1692e418d7ccac24c373528453bc90e42797de',
    ),
    'symfony/debug' => 
    array (
      'pretty_version' => 'v2.7.51',
      'version' => '2.7.51.0',
      'aliases' => 
      array (
      ),
      'reference' => '4a7330f29b3d215f8bacf076689f9d1c3d568681',
    ),
    'symfony/dom-crawler' => 
    array (
      'pretty_version' => 'v2.7.51',
      'version' => '2.7.51.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd905e1c5885735ee66af60c205429b9941f24752',
    ),
    'symfony/event-dispatcher' => 
    array (
      'pretty_version' => 'v2.8.52',
      'version' => '2.8.52.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a77e974a5fecb4398833b0709210e3d5e334ffb0',
    ),
    'symfony/finder' => 
    array (
      'pretty_version' => 'v2.7.51',
      'version' => '2.7.51.0',
      'aliases' => 
      array (
      ),
      'reference' => '34226a3aa279f1e356ad56181b91acfdc9a2525c',
    ),
    'symfony/http-foundation' => 
    array (
      'pretty_version' => 'v2.7.51',
      'version' => '2.7.51.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b67e5cbd2bf837fb3681f2c4965826d6c6758532',
    ),
    'symfony/http-kernel' => 
    array (
      'pretty_version' => 'v2.7.52',
      'version' => '2.7.52.0',
      'aliases' => 
      array (
      ),
      'reference' => '435064b3b143f79469206915137c21e88b56bfb9',
    ),
    'symfony/polyfill-ctype' => 
    array (
      'pretty_version' => 'v1.19.0',
      'version' => '1.19.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'aed596913b70fae57be53d86faa2e9ef85a2297b',
    ),
    'symfony/polyfill-intl-idn' => 
    array (
      'pretty_version' => 'v1.19.0',
      'version' => '1.19.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '4ad5115c0f5d5172a9fe8147675ec6de266d8826',
    ),
    'symfony/polyfill-intl-normalizer' => 
    array (
      'pretty_version' => 'v1.19.0',
      'version' => '1.19.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '8db0ae7936b42feb370840cf24de1a144fb0ef27',
    ),
    'symfony/polyfill-mbstring' => 
    array (
      'pretty_version' => 'v1.19.0',
      'version' => '1.19.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b5f7b932ee6fa802fc792eabd77c4c88084517ce',
    ),
    'symfony/polyfill-php56' => 
    array (
      'pretty_version' => 'v1.19.0',
      'version' => '1.19.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'ea19621731cbd973a6702cfedef3419768bf3372',
    ),
    'symfony/polyfill-php70' => 
    array (
      'pretty_version' => 'v1.19.0',
      'version' => '1.19.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '3fe414077251a81a1b15b1c709faf5c2fbae3d4e',
    ),
    'symfony/polyfill-php72' => 
    array (
      'pretty_version' => 'v1.19.0',
      'version' => '1.19.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'beecef6b463b06954638f02378f52496cb84bacc',
    ),
    'symfony/polyfill-util' => 
    array (
      'pretty_version' => 'v1.19.0',
      'version' => '1.19.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '8df0c3e6a4b85df9a5c6f3f2f46fba5c5c47058a',
    ),
    'symfony/process' => 
    array (
      'pretty_version' => 'v2.7.51',
      'version' => '2.7.51.0',
      'aliases' => 
      array (
      ),
      'reference' => 'eda637e05670e2afeec3842dcd646dce94262f6b',
    ),
    'symfony/routing' => 
    array (
      'pretty_version' => 'v2.7.51',
      'version' => '2.7.51.0',
      'aliases' => 
      array (
      ),
      'reference' => '33bd5882f201f9a3b7dd9640b95710b71304c4fb',
    ),
    'symfony/translation' => 
    array (
      'pretty_version' => 'v2.7.51',
      'version' => '2.7.51.0',
      'aliases' => 
      array (
      ),
      'reference' => '1959c78c5a32539ef221b3e18a961a96d949118f',
    ),
    'symfony/var-dumper' => 
    array (
      'pretty_version' => 'v2.7.51',
      'version' => '2.7.51.0',
      'aliases' => 
      array (
      ),
      'reference' => '6f9271e94369db05807b261fcfefe4cd1aafd390',
    ),
    'tijsverkoyen/css-to-inline-styles' => 
    array (
      'pretty_version' => '2.2.3',
      'version' => '2.2.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b43b05cf43c1b6d849478965062b6ef73e223bb5',
    ),
    'vlucas/phpdotenv' => 
    array (
      'pretty_version' => 'v1.1.1',
      'version' => '1.1.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '0cac554ce06277e33ddf9f0b7ade4b8bbf2af3fa',
    ),
    'w7corp/easywechat' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '9999999-dev',
      ),
      'reference' => '59a950ecf0104b879623ee5e5c82cf49c0e791cb',
    ),
  ),
);
private static $canGetVendors;
private static $installedByVendor = array();







public static function getInstalledPackages()
{
$packages = array();
foreach (self::getInstalled() as $installed) {
$packages[] = array_keys($installed['versions']);
}

if (1 === \count($packages)) {
return $packages[0];
}

return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
}









public static function isInstalled($packageName)
{
foreach (self::getInstalled() as $installed) {
if (isset($installed['versions'][$packageName])) {
return true;
}
}

return false;
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

$ranges = array();
if (isset($installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = $installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['version'])) {
return null;
}

return $installed['versions'][$packageName]['version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getPrettyVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return $installed['versions'][$packageName]['pretty_version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getReference($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['reference'])) {
return null;
}

return $installed['versions'][$packageName]['reference'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getRootPackage()
{
$installed = self::getInstalled();

return $installed[0]['root'];
}








public static function getRawData()
{
@trigger_error('getRawData only returns the first dataset loaded, which may not be what you expect. Use getAllRawData() instead which returns all datasets for all autoloaders present in the process.', E_USER_DEPRECATED);

return self::$installed;
}







public static function getAllRawData()
{
return self::getInstalled();
}



















public static function reload($data)
{
self::$installed = $data;
self::$installedByVendor = array();
}





private static function getInstalled()
{
if (null === self::$canGetVendors) {
self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
}

$installed = array();

if (self::$canGetVendors) {
foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
if (isset(self::$installedByVendor[$vendorDir])) {
$installed[] = self::$installedByVendor[$vendorDir];
} elseif (is_file($vendorDir.'/composer/installed.php')) {
$installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
}
}
}

$installed[] = self::$installed;

return $installed;
}
}
