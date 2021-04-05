# variable-parser
Available variable names: userName, user.name, user-name, user_name, UserName, user_Name

php artisan vendor:publish --tag=variable-parser-config
```PHP
<?php

return [
    'path' => '', // Path to variables
    'signOpen' => '[[',
    'signClose' => ']]',
    'variableFrom' => [
        'class' => false,
        'relation' => true
    ]
];
```

```PHP
use Bizarg\VariableParser\VariableParser;

$variableData = ['user' => User::find(1)]; /*or*/  (new VariableData())->setUser(User::find(1));

$content = 'Name: [[user.name]]<br>
    Email: [[user.email]]<br>
    Title: [[article.title]]<br>
    Custom: [[custom]]<br>
    Slug: [[article.slug]]<br>';

$parser = new VariableParser();
$parser->setContent($content);//string
$parser->setVariableData($variableData);//array|object
$content = $parser->parseContent();

$content = (new VariableParser($content, $variableData))->parseContent();

$parser->setData([
    'user.name' => 'White Wolf',
    'custom' => 'value',
]);
$parser->setSignOpen('{{');
$parser->setSignClose('}}');
$parser->setPreview(true);// if used class
$parser->setContent('Name: [[user.name]]<br>');
```
