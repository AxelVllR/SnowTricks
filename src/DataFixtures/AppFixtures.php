<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\Groups;
use App\Entity\Tricks;
use App\Entity\TricksPictures;
use App\Entity\Users;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private const Groups = [
        "Grabs" => "primary",
        "Rotations" => "secondary",
        "Flips" => "success",
        "Slides" => "danger",
        "One Foot Tricks" => "warning",
        "Old School" => "info"
    ];

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        // Create Dirs
        if(!file_exists(__DIR__ . '/../../public/images/tricks')) {
            mkdir(__DIR__ . '/../../public/images/tricks');
        }
        if(!file_exists(__DIR__ . '/../../public/images/users')) {
            mkdir(__DIR__ . '/../../public/images/users');
        }
        // Create Groups
        foreach(self::Groups as $group => $color) {
            $gp = (new Groups())
                ->setName($group)
                ->setColor($color)
            ;
            $manager->persist($gp);
        }

        $manager->flush();

        $this->saveImage("https://www2.assemblee-nationale.fr/static/tribun/15/photos/721568.jpg", "userLogoExample.jpg");

        // Create Users and save them
        $users = [];

        //ME
        $userMe = (new Users())
            ->setFilename("userLogoExample.jpg")
            ->setPseudo("AxelVllR")
            ->setEmail("vallieraxel@gmail.com")
            ->setIsActivated(true)
            ->setPassword(password_hash("axelou", PASSWORD_ARGON2I));
        $manager->persist($userMe);

        $i = 0;
        while($i < 20) {
            $i++;
            $user = (new Users())
                ->setFilename("userLogoExample.jpg")
                ->setPassword(password_hash("admin", PASSWORD_ARGON2I))
                ->setEmail($faker->email)
                ->setPseudo($faker->userName);
            $manager->persist($user);
            $users[] = $user;
        }

        //set Tricks
        $jsonPath = __DIR__ . '/../../tricks_data.json';
        $json = json_decode(file_get_contents($jsonPath), true);
        foreach($json as $group => $tricks) {
            $group = $manager->getRepository(Groups::class)->findOneBy(['name' => $group]);
            foreach($tricks as $name => $values) {
                $key = array_rand($users);
                $trick = (new Tricks())
                    ->setCreatedAt(new \DateTime('now'))
                    ->setCreatedBy($users[$key])
                    ->setName($name)
                    ->setDescription($values['description'])
                    ->setGroupTrick($group);
                $manager->persist($trick);

                foreach($values['image_url'] as $key => $imageUrl) {
                    $exp = explode("/", $imageUrl);
                    $file = end($exp);
                    $explodeFileName = explode("?", $file);
                    $url = $explodeFileName[0];
                    $this->saveImageTrick($imageUrl, $url);
                    $image = (new TricksPictures())
                        ->setFilename($url)
                        ->setTricks($trick);

                    $manager->persist($image);
                }

                foreach($values['video_url'] as $key => $url) {
                    $video = (new Video())
                        ->setTrick($trick)
                        ->setUrl($url);

                    $manager->persist($video);
                }

                $randomInt = random_int(15, 40);
                $i = 0;
                while($i < $randomInt) {
                    $i ++;
                    $key = array_rand($users);

                    $comment = (new Comments())
                        ->setTrick($trick)
                        ->setCreatedAt(new \DateTime('now'))
                        ->setUser($users[$key])
                        ->setContent($faker->text(150));

                    $manager->persist($comment);
                }
            }
        }


        $manager->flush();
    }

    public function saveImage($url, $filename) {
        $data = $this->file_get_contents_curl($url);

        $fp = __DIR__ . '/../../public/images/users/' . $filename;

        file_put_contents( $fp, $data );
    }
    public function saveImageTrick($url, $filename) {
        $data = $this->file_get_contents_curl($url);

        $fp = __DIR__ . '/../../public/images/tricks/' . $filename;

        file_put_contents( $fp, $data );
    }

    public function file_get_contents_curl($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}
