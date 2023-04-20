<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use App\Entity\Phone;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasher = $userPasswordHasherInterface;
    }

    public function getPhonesData() : array 
    {
        $phones = [
            [
                'label' => 'Huawei Honor Magic5 Lite',
                'brand' => 'Huawei',
                'price' => '350.00',
                'os' => 'Android 12, Magic UI 6.1',
                'cpu' => 'Octa-core (2x2.2 GHz Kryo 660 Gold & 6x1.7 GHz Kryo 660 Silver)',
                'screen' => '6.67" pouces, 1080 x 2400, AMOLED',
                'isAvailable' => true
            ],
            [
                'label' => 'Doogee V Max',
                'brand' => 'Doogee',
                'price' => '380.00',
                'os' => 'Android 12',
                'cpu' => 'Octa-core (2x2.6 GHz Cortex-A78 & 6x2.0 GHz Cortex-A55)',
                'screen' => '6.58" pouces, 1080 x 2408, IPS LCD',
                'isAvailable' => true
            ],
            [
                'label' => 'Vivo Y100',
                'brand' => 'Vivo',
                'price' => '300.00',
                'os' => 'Android 13, Funtouch 13',
                'cpu' => '6.38" pouces, 1080 x 2400, AMOLED',
                'screen' => '6.38" pouces, 1080 x 2400, AMOLED',
                'isAvailable' => true
            ],
            [
                'label' => 'OnePlus Ace 2',
                'brand' => 'OnePlus',
                'price' => '599.99',
                'os' => 'Android 13, ColorOS 13',
                'cpu' => 'Octa-core (1x3.19 GHz Cortex-X2 & 3x2.75 GHz Cortex-A710 & 4x1.80 GHz Cortex-A510)',
                'screen' => '6.74" pouces, 1240 x 2772, AMOLED',
                'isAvailable' => true
            ],
            [
                'label' => 'Samsung Galaxy S23+',
                'brand' => 'Samsung',
                'price' => '740.00',
                'os' => 'Android 13, One UI 5.1',
                'cpu' => 'Octa-core (1x3.36 GHz Cortex-X3 & 2x2.8 GHz Cortex-A715 & 2x2.8 GHz Cortex-A710 & 3x2.0 GHz Cortex-A510)',
                'screen' => '6.6" pouces, 1080 x 2340, Dynamic AMOLED 2X',
                'isAvailable' => true
            ],
            [
                'label' => 'Samsung Galaxy A34 5G',
                'brand' => 'Samsung',
                'price' => '900.00',
                'os' => 'Android 13, One UI 5.1',
                'cpu' => 'Octa-core (2x2.6 GHz Cortex-A78 & 6x2.0 GHz Cortex-A55)',
                'screen' => '6.6" pouces, 1080 x 2340, Super AMOLED',
                'isAvailable' => false
            ],
            [
                'label' => 'Oppo A78 5G',
                'brand' => 'Oppo',
                'price' => '590.00',
                'os' => 'Android 13',
                'cpu' => 'Octa-Core (4x Cortex A76 2.2 GHz + 4x Cortex A55 2.0 GHz)',
                'screen' => '7.51" pouces, 720 x 1612, 188 g',
                'isAvailable' => true
            ],
            [
                'label' => 'Apple iPhone 11 Pro Max',
                'brand' => 'Apple',
                'price' => '1199.99',
                'os' => 'iOS 13.2',
                'cpu' => '6-core (2x2.65 GHz Lightning + 4x1.8 GHz Thunder)',
                'screen' => 'Super Retina XDR OLED',
                'isAvailable' => true
            ],
            [
                'label' => 'Nokia C12 Plus',
                'brand' => 'Nokia',
                'price' => '497.00',
                'os' => 'Android 12 (Go edition)',
                'cpu' => '8-core (4x1.6 GHz Cortex-A55 + 4x1.2 GHz Cortex-A55)',
                'screen' => 'IPS LCD',
                'isAvailable' => false
            ],
            [
                'label' => 'Cubot P80',
                'brand' => 'Cubot',
                'price' => '753.00',
                'os' => 'Android 13 Tiramisu',
                'cpu' => '8-Core (4xCortex A73 2.0GHz + 4xCortex A53 2.0GHz)',
                'screen' => 'LCD IPS 6.58" pouces',
                'isAvailable' => true
            ],
            [
                'label' => 'Meizu 20 Infinity',
                'brand' => 'Meizu',
                'price' => '1399.99',
                'os' => 'Flyme 10',
                'cpu' => '8-core (1x3.2 GHz Cortex-X3 + 2x2.8 GHz Cortex-A715 + 2x2.8 GHz Cortex-A710 + 3x2.0 GHz Cortex-A510)',
                'screen' => 'LTPO OLED 6.79" pouces',
                'isAvailable' => true
            ],
            [
                'label' => 'Tecno Spark 10C',
                'brand' => 'Tecno Spark',
                'price' => '899.99',
                'os' => 'Android, HIOS 8.6',
                'cpu' => '8-Core (4xCortex A73 2.0GHz + 4xCortex A53 2.0GHz)',
                'screen' => 'IPS LCD 6.6" pouces',
                'isAvailable' => true
            ],
            [
                'label' => 'Infinix Zero',
                'brand' => 'Infinix',
                'price' => '910.00',
                'os' => 'Android 12, XOS 12',
                'cpu' => 'Octa-core (2x2.6 GHz Cortex-A78 & 6x2.0 GHz Cortex-A55) - X6815D',
                'screen' => '6.78" pouces, 1080 x 2460, IPS LCD',
                'isAvailable' => true
            ]
        ];

        return $phones;
    }

    public function load(ObjectManager $manager): void
    {
        $phones = $this->getPhonesData();
        foreach($phones as $phoneData) {
            $phone = new Phone();
            $phone  
                ->setLabel($phoneData['label'])
                ->setBrand($phoneData['brand'])
                ->setPrice($phoneData['price'])
                ->setOs($phoneData['os'])
                ->setCpu($phoneData['cpu'])
                ->setScreen($phoneData['screen'])
                ->setIsAvailable($phoneData['isAvailable'])
            ;
            $manager->persist($phone);
        }

        $faker = Faker\Factory::create('fr_FR');
        $users = [];
        $roles = array('ROLE_USER', 'ROLE_ADMIN');
        for ($i = 0; $i < 10; $i++) {
            $users[$i] = new User();
            $users[$i]->setLabel($faker->unique()->company.' '.$faker->randomElement(['Marketplace', 'Shop', 'Store']));
            $users[$i]->setEmail($faker->unique()->email);
            $users[$i]->setPassword($this->userPasswordHasher->hashPassword($users[$i], 'password'));
            $randomKey = array_rand($roles, 1);
            $users[$i]->setRoles([$roles[$randomKey]]);

            $manager->persist($users[$i]);
        }

        $customers = [];
        for ($i = 0; $i < 50; $i++) {
            $customers[$i] = new Customer();
            $customers[$i]->setName($faker->unique()->lastname);
            $customers[$i]->setNickname($faker->unique()->firstname);
            $customers[$i]->setEmail($faker->unique()->email);
            $customers[$i]->setAdress($faker->address);
            $randomUser = array_rand($users, 1);
            $customers[$i]->setMarketplace($users[$randomUser]);

            $manager->persist($customers[$i]);
        }

        $manager->flush();
    }
}
