
This is library for simple elasticsearch usage

###### This library can do two main things:
1. retroactive indexing from your existing database and
2. partial indexing records, update, delete and finally search

For first part of functionality you have to make 'elasticadapted' directory under /data/ directory.
In that directory you have to make two files:
-``retroactiveIndexingConfig.php``
-``doRetroactiveIndexing.php``
In first file you have to provide configuration array respectively under variable name ``$retroactiveIndexingConfig``.
There are an example of that array:
```
$retroactiveIndexingConfig = [
    'elasticHosts'=>[
        '172.17.0.2:9200'
    ],
    'dbConfig'=>[
        'dbName'=>'shop',
        'dbHost'=>'shop-mysql',
        'dbUser'=>'root',
        'dbPass'=>'root',
    ],
    'indexingConfig'=>[
        \Application\Entity\Category::class=>[
            'queryConfig'=>[
                'fields'=>['category_name', 'category_description'],
                'table'=>'category',
            ],
            'indexConfig'=>[
                'index'=>'shop',
                'id'=>'id'
            ]
        ],
        \Application\Entity\MeasureUnit::class=>[
            'queryConfig'=>[
                'fields'=>['measure_unit_name', 'measure_unit_mark'],
                'table'=>'measure_unit',
            ],
            'indexConfig'=>[
                'index'=>'shop',
                'id'=>'id'
            ]
        ],//...
    ]
];
```
``queryConfig`` contains table name from your MySQL database and name of fields from that table that you want to index.
``indexConfig`` contains index name and name of ``id`` column from MySQL database table. 

In second file you have to put following code:
```
require_once "vendor/autoload.php";
require_once "retroactiveIndexingConfig.php";

\ElasticAdapted\ElasticRetroactiveIndexing::createRetroactiveIndexing($retroactiveIndexingConfig)->doIndexing();
```
Then in your docker web container run following command:
```
php data/elasticadapted/doRetroactiveIndexing.php
```


### Configuration for second part of functionality

In your ``global.php`` file you have to provide following configuration array:
```
'elastic_config'=>[
        'hosts'=>[
            '172.17.0.2:9200'
        ],
        'indexing'=>[
            \Application\Entity\Product::class=>['name', 'description'],
            \Application\Entity\Category::class=>['categoryName', 'categoryDescription']
        ],
    ],
```
``indexing`` part of this array provides, like keys, entity names whose fields you want to index, 
and like values, arrays with entity field names that you want to index.

#### Indexing
In factory of service where you are adding record by ``entityManager``, through factory of this service, 
you have to provide instance of ElasticManager like this:
```
$config = ($container->get('config'))['elastic_config'];
$elasticManager = new ElasticManager($config);
```
Now, you can inject ``$elasticManager``  in service constructor.

After calling method ``flush()`` in your service 
you have to provide this code:
```
$elasticIndexing = $this->elasticManager->getElasticIndexing();
$elasticIndexing->setIndex('indexName');
$elasticIndexing->setEntityInstance($instanceOfTargetedEntity);
$elasticIndexing->indexing();
```
where ``$instanceOfTargetedEntity`` is entity instance that you put in entity manager ``persist()`` method;

#### Searching
1. get searching
```
$elasticSearching = $this->elasticManager->getElasticSearching();
$elasticSearching->setIndex('indexName');
$elasticSearching->setId("idName");
$result = $elasticSearching->getById();    
```
2. match searching 
```
$elasticSearching = $this->elasticManager->getElasticSearching();
$elasticSearching->setIndex('indexName');
$result = $elasticSearching->matchSearching(['fieldName'=>'word']);  
```
3. should match searching
```
$elasticSearching = $this->elasticManager->getElasticSearching();
$elasticSearching->setIndex('indexName');
$result = $elasticSearching->shouldMatchSearching([['fieldName'=>'word'],['fieldName'=>'word']]);
```
4. must match searching
```
$elasticSearching = $this->elasticManager->getElasticSearching();
$elasticSearching->setIndex('indexName');
$result = $elasticSearching->mustMatchSearching([['fieldName'=>'word'],['fieldName'=>'word']]);
```
5. should wildcard searching
```
$elasticSearching = $this->elasticManager->getElasticSearching();
$elasticSearching->setIndex('indexName');
$result = $elasticSearching->shouldWildcardsSearching([['fieldName'=>'word'],['fieldName'=>'word']]);
```
6. must wildcard searching
```
$elasticSearching = $this->elasticManager->getElasticSearching();
$elasticSearching->setIndex('indexName');
$result = $elasticSearching->mustWildcardsSearching([['fieldName'=>'word'],['fieldName'=>'word']]);
```
For every indexed document is provided ``searching_content`` field, and in that field are contents from all indexed fields, it makes this possible:
```
$elasticSearching = $this->elasticManager->getElasticSearching();
$elasticSearching->setIndex('indexName');
$result = $elasticSearching->shouldWildcardsSearching([['searching_content'=>'*phraze*']]);
```
#### Deleting
1. Deleting all by index name
```
$elasticDeleting = $this->elasticManager->getElasticDeleting();
$elasticDeleting->deleteAllByIndex('indexName');
```
2. Deleting by id
```
$elasticDeleting = $this->elasticManager->getElasticDeleting();
$elasticDeleting->setId('idName');
$elasticDeleting->deleting();
```

#### Updating
```
$elasticUpdating = $this->elasticManager->getElasticUpdating();
$elasticUpdating->setIndex('indexName');
$elasticUpdating->setEntityInstance($instanceOfTargetedEntity);
$elasticUpdating->updating();
```