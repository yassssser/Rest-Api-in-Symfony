<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $this->loadUser($manager);
        $this->loadPost($manager);
        $this->loadComments($manager);
        
    }

    public function loadPost(ObjectManager $manager){
        for($i=0 ; $i<10 ; $i++){
            $post = new Post();
            $post->setTitle($this->faker->sentence());
            $post->setContent($this->faker->realText());
            $post->setPublished(new \DateTime());
            $post->setSlug($this->faker->slug());

            $user = $this->getReference("user_".rand(0,9));
            $this->addReference("post_$i", $post);

            $post->setAuthor($user);
            
            $manager->persist($post);
        }
            $manager->flush();
    }
    public function loadUser(ObjectManager $manager){

        $roles = [User::ROLE_SUPERADMIN,User::ROLE_ADMIN,User::ROLE_EDITOR,User::ROLE_WRITER,User::ROLE_COMMENTATOR];

        for($i=0 ; $i<10 ; $i++){
            $user = new User();
            $user->setName($this->faker->name);

             $user->setPassword($this->passwordEncoder->encodePassword($user , 'sercet123'));

            $user->setEmail($this->faker->email);
            $user->setRoles([$roles[rand(0, 4)]]);

            $this->addReference("user_$i", $user);

            $manager->persist($user);
        }
        $manager->flush();
    }

    public function loadComments(ObjectManager $manager){
        for($i=0 ; $i<10 ; $i++){

            $comment = new Comment();
            $comment->setContent($this->faker->realText());
            $comment->setPublished(new \DateTime());

            $user = $this->getReference("user_".rand(0,9));
            $post = $this->getReference("post_$i");

            $comment->setAuthor($user);
            $comment->setPost($post);

            $manager->persist($comment);
        }
        $manager->flush();
    }
}
