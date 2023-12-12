<?php

function alura_intercambios_register_menu()
{
    register_nav_menu('menu-navegacao', 'Menu navegação');
}

add_action('init', 'alura_intercambios_register_menu');

function alura_intercambios_adicionando_recursos_ao_tema()
{
    add_theme_support('custom-logo');
    add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'alura_intercambios_adicionando_recursos_ao_tema');

function alura_intercambios_registrando_post_customizado()
{
    register_post_type('destinos', [
        'labels' => [
            'name' => 'Destinos',
        ],
        'public' => true,
        'menu_position' => 0,
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_icon' => 'dashicons-admin-site',
    ]);
}

add_action('init', 'alura_intercambios_registrando_post_customizado');

function alura_intercambios_registrando_taxonomia()
{
    // Associar a taxonomia ao tipo de custom post
    register_taxonomy('paises', 'destinos', [
        'labels' => ['name' => 'Paises'],
        'hierarchical' => true
    ]);
}

add_action('init', 'alura_intercambios_registrando_taxonomia');

function alura_intercambios_registrando_post_customizado_banner()
{
    register_post_type(
        'banners',
        [
            'labels' => [
                'name' => 'Banner',
            ],
            'public' => true,
            'menu_position' => 1,
            'menu_icon' => 'dashicons-format-image',
            'supports' => ['title', 'thumbnail'],
        ]
    );
}

add_action('init', 'alura_intercambios_registrando_post_customizado_banner');

function alura_intercambios_registrando_metabox()
{
    add_meta_box(
        'ai_registrando_metabox',
        'Texto para a home',
        'ai_funcao_callback',
        'banners',
    );
}

add_action('add_meta_boxes', 'alura_intercambios_registrando_metabox');

function ai_funcao_callback($post)
{
    // O true retorna somente o valor em vez de array
    // get_post_meta serve para preencher os dados dos campos associados ao post
    $texto_home_1 = get_post_meta($post->ID, '_texto_home_1', true);
    $texto_home_2 = get_post_meta($post->ID, '_texto_home_2', true);
?>
    <label for="texto_home_1">Texto 1</label>
    <input type="text" name="texto_home_1" style="width: 100%;" value="<?= $texto_home_1 ?>"/>
    <br>
    <br>
    <label for="texto_home_2">Texto 2</label>
    <input type="text" name="texto_home_2" style="width: 100%;" value="<?= $texto_home_2 ?>"/>
<?php
}


function alura_intercambios_salvando_dados_metabox($post_id) {
    foreach($_POST as $key => $value) {
        if($key !== 'texto_home_1' && $key !== 'texto_home_2') {
            continue;
        }

        update_post_meta($post_id, '_'. $key, $_POST[$key]);
    }
}

add_action('save_post', 'alura_intercambios_salvando_dados_metabox');

function pegandoTextosParaBanner() {

    $args = [
        'post_type' => 'banners',
        'post_status' => 'publish',
        'posts_per_page' => 1
    ];

    $query = new WP_Query($args);

    if($query->have_posts()) {
        while($query->have_posts()) {
            $query->the_post();
            $texto1 = get_post_meta(get_the_ID(), '_texto_home_1', true);
            $texto2 = get_post_meta(get_the_ID(), '_texto_home_2', true);
            return [
                'texto_1' => $texto1,
                'texto_2' => $texto2
            ];
        }
    }
}


function alura_intercambios_adicionando_scripts() {

    $textosBanner = pegandoTextosParaBanner();    

    if(is_front_page()) {
        wp_enqueue_script('typed-js', get_template_directory_uri() . '/js/typed.min.js', array(), false, true);
        wp_enqueue_script('texto-banner-js', get_template_directory_uri() . '/js/texto-banner.js', array('typed-js'), false, true);
        // Para passar os textos para o texto-banner-js
        wp_localize_script('texto-banner-js', 'data', $textosBanner);
    }
}

add_action('wp_enqueue_scripts', 'alura_intercambios_adicionando_scripts');