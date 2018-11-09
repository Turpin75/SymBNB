<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Appfixtures extends Fixture
{
    private $encoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        $adminUser = new User();
        $adminUser->setFirstName("Joseph")
                  ->setLastName("Turpin")
                  ->setPseudo("Pajo")
                  ->setEmail("testturpin@gmail.com")
                  ->setPassword($this->encoder->encodePassword($adminUser, "1234"))
                  ->setIntroduction("Je m'appelle Joseph Turpin")
                  ->setDescription("Je m'appelle Joseph Turpin et j'ai 30 ans.")
                  ->setPicture("https://randomuser.me/api/portraits/lego/1.jpg")
                  ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        
        $manager->persist($adminUser);
        
        // Nous gérons les utilisateurs
        $users = [];

        for($i = 1; $i <= 10; $i++)
        {
            $user = new User();

            $genre = $faker->randomElement(["male", "female"]);

            $picture = "https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1, 99).".jpg";
            $picture .= ($genre == "male" ? "men/" : "women/") . $pictureId;

            $password = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname)
                ->setLastName($faker->lastname)
                ->setPseudo($faker->firstname)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence)
                ->setDescription("<p>".join("</p><p>", $faker->paragraphs(5))."</p>")
                ->setPassword($password)
                ->setPicture($picture);
            
            $manager->persist($user);
            $users[] = $user;

        }
        
        // Nous gérons les annonces
        for($i = 1; $i <= 30; $i++)
        {
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = "<p>".join("</p><p>", $faker->paragraphs(5))."</p>";
            
            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);
        
            for($j = 1; $j <= mt_rand(2, 5); $j++)
            {
                $image = new Image();
                $image->setUrl($faker->imageUrl)
                        ->setCaption($faker->sentence())
                        ->setAd($ad);
                
                $manager->persist($image);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
