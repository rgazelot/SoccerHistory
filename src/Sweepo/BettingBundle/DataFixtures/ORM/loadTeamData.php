<?php

namespace Sweepo\BettingBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Sweepo\BettingBundle\Entity\Team;
use Sweepo\BettingBundle\Entity\League;

class LoadTeamData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $dirname = __DIR__ . "/../../../../../team/";
        $dir = opendir($dirname);

        while($file = readdir($dir)) {
            if($file != '.' && $file != '..' && !is_dir($dirname.$file))
            {
                $content = file_get_contents(__DIR__ . "/../../../../../team/" . $file);
                $array = explode("\n", $content);

                // League
                $ar = explode(':', $array[0]);
                $name = $ar[1];
                $league = new League();
                $league->setName($name);
                unset($array[0]);

                // Country
                $ar = explode(':', $array[1]);
                $country = $ar[1];
                $league->setCountry($country);
                unset($array[1]);
                $this->setReference($name, $league);
                $manager->persist($league);

                foreach ($array as $value) {
                    $team = new Team();
                    $team->setName($value);
                    $team->setLeague($league);
                    $this->setReference($value, $team);
                    $manager->persist($team);
                }
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 20;
    }
}