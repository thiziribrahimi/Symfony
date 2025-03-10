# symfony

## Installation

Créez le fichier docker-compose.yml

```bash
$ docker-compose up -d --build
$ docker-compose exec web symfony new . --version="7.2.x" --webapp --no-interaction
```
## Création du controller

```bash
$ touch src/Controller/LuckyController.php
```

*src/Controller/LuckyController.php*

```php
<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuckyController extends AbstractController
{
    #[Route('/lucky/number')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        /*return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );*/
        return $this->render('lucky/number.html.twig', [
            'toto' => $number,
        ]);
    }
}
```

## Créer la base de donnée

```bash
php bin/console doctrine:database:create
php bin/console make:entity Music
docker-compose exec web php bin/console make:migration
docker-compose exec web php bin/console d:m:m
```


## Création du formulaire des Music

```bash
docker-compose exec web php bin/console make:form
```

On apporte quelques modifications

```php
<?php

namespace App\Form;

use App\Entity\Music;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MusicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Entrez le nom',
                'attr' => [
                    'placeholder' => 'test'
                ]
            ])
            ->add('url', TextType::class)
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Music::class,
        ]);
    }
}
```

### Le controller

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MusicRepository;
use App\Form\MusicType;
use App\Entity\Music;

final class MusicController extends AbstractController
{
    #[Route('/', name: 'app_music')]
    public function index(
        MusicRepository $musicRepo
    ): Response
    {
        $musics = $musicRepo->findAll();

        return $this->render('music/index.html.twig', [
            'musicsList' => $musics,
        ]);
    }

    #[ROUTE('/music/new', name: "app_music_new")]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $music = new Music();
        $form = $this->createForm(MusicType::class, $music);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $music = $form->getData();

            $entityManager->persist($music);
            $entityManager->flush();

            return $this->redirectToRoute('app_music');
        }

        return $this->render('music/new.html.twig', [
            'form' => $form,
        ]);
    }
}

```

Le template pour afficher la liste

```html
{% extends 'base.html.twig' %}

{% block title %}YouMusic !{% endblock %}

{% block body %}
    <h1>Liste des musiques disponibles</h1>

    <a href="{{ path('app_music_new') }}">Ajouter une musique</a>

    <ul>
        {% for music in musicsList %}
            <li>{{ music.name }} - {{ music.author }}</li>
        {% endfor %}
    </ul>
{% endblock %}

```

Le template pour afficher le formulaire

```html
{% extends 'base.html.twig' %}

{% block title %}YouMusic !{% endblock %}

{% block body %}
    <h1>Créer une nouvelle musique</h1>

    {{ form(form) }}

    <a href="{{ path('app_music') }}">Retour</a>
{% endblock %}
```

## Modification de Music pour ajouter l'auteur

```bash
docker-compose exec web php bin/console make:entity Music
docker-compose exec web php bin/console make:migration
docker-compose exec web php bin/console d:m:m
```

### Installation de FakerPHP

```bash
 docker-compose exec web composer require fakerphp/faker
```

### Modification des fixtures

```php
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Music;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=1; $i <= 10; $i++) {
            $music = new Music();
            $music->setName("Music" . $i);
            $music->setUrl("url" . $i);
            $music->setAuthor($faker->name());
            
            $manager->persist($music);
        }

        $manager->flush();
    }
}
```

### Executer les fixtures

```bash
docker-compose exec web php bin/console d:f:l --append
```

## Eaasy Bundle Admin

```bash
docker-compose exec web composer require easycorp/easyadmin-bundle
docker-compose exec web php bin/console make:admin:dashboard
docker-compose exec web php bin/console cache:clear
docker-compose exec web php bin/console make:admin:crud
```