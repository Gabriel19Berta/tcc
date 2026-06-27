<?php
return [
    'clientes' => [
        'titulo' => 'Clientes',
        'icone' => 'fa-solid fa-users',
        'relatorios' => [
            'geral' => [
                'titulo' => 'Relação Geral',

                'filtros' => [
                    'status'
                ]
            ]
        ]
    ],

    'produtos' => [
        'titulo' => 'Produtos',
        'icone' => 'fa-solid fa-box',
        'relatorios' => [
            'geral' => [
                'titulo' => 'Relação Geral',

                'filtros' => [
                    'status'
                ]
            ],

            'marca' => [

                'titulo' => 'Produtos por Marca'

            ]

        ]

    ]

];
?>