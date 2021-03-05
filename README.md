# variable-parser
Available variable names: userName, user.name, user-name, user_name, UserName, user_Name

php artisan vendor:publish --tag=variable-parser-config
```PHP
<?php

return [
    'path' => '', // Path to variables
    'signOpen' => '[[',
    'signClose' => ']]'
];
```

```PHP
use Bizarg\VariableParser\VariableParser;

$variableData = [
    'userName' => 'White Wolf'
];

//or

$variableData = (new VariableData())->setUser($user);

$content = 'Name: [[user.Name]]<br>Email: [[userEmail]]<br>Title: [[articleTitle]]<br>Slug: [[articleSlug]]<br>';

$parser = new VariableParser($content, $variableData);
$parser->parseContent();

$parser->setData(['userName' => 'White Wolf']);
$parser->setSignOpen('{{');
$parser->setSignClose('}}');
$parser->setPreview(true);
$parser->setContent('Name: [[user.Name]]<br>');
```
