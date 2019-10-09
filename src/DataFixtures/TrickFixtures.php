<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Comment;
use App\Entity\Category;
use App\Utils\Slugger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TrickFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('FR-fr');

        // Fake User
        $users = [];
        for ($u = 0; $u < 6; $u++){
            $user = new User();
            $user->setPseudo($faker->userName)
                 ->setEmail($faker->safeEmail)
                 ->setPassword($this->encoder->encodePassword($user, 'password'))
                 ->setConfirm(1)
                 ->setToken(bin2hex(random_bytes(16)));
            $manager->persist($user);
            $users[] = $user;
        }

        // Fake Categories
        $categories = [];
        $categoriesName = ['Grabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot', 'Old school'];
        foreach ($categoriesName as $categoryName)
        {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Fake Tricks
        $tricksName = ['Mute', 'Indy', '360', '720', 'Backflip', 'Misty', 'Tail slide', 'Method air', 'Backside air'];
        foreach ($tricksName as $trickName)
        {
            $trick = new Trick();
            $trick->setName($trickName)
                  ->setDescription($faker->paragraph(mt_rand(2, 4)))
                  ->setSlug(Slugger::slugify($trick->getName()))
                  ->setAuthor($faker->randomElement($users))
                  ->setCategory($faker->randomElement($categories));

            $manager->persist($trick);

            // Fake images
            for ($p = 1; $p < 5; $p++)
            {
                $image = new Image();
                $image->setSrc($trick->getName() . '-' . $p . '.jpg')
                      ->setAlt('Photo du trick ' . $trick->getName())
                      ->setTrick($trick);

                $manager->persist($image);
            }

            // Fake Comments
            for ($c = 0; $c < mt_rand(0, 5); $c++)
            {
                $comment = new Comment();
                $comment->setContent($faker->sentence(mt_rand(1, 5)))
                        ->setCreatedAt($faker->dateTimeBetween($startDate = $trick->getCreatedAt(), $endDate = 'now'))
                        ->setAuthor($faker->randomElement($users))
                        ->setTrick($trick);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
