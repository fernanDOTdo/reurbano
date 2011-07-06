<?php

namespace Reurbano\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_Function_Method;
use Twig_Filter_Method;
use DateTime;
use IntlDateFormatter;

class ReurbanoCoreExtension extends Twig_Extension
{
    protected $container;
    protected $dateFormatter;

    /**
     * Constructor.
     *
     * @param Router $router A Router instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        $mappings = array(
            'reurbano_session'           => 'getSession',
            'reurbano_user_text'         => 'userText',
            'reurbano_shorten'           => 'shorten',
            'reurbano_current_url'       => 'getCurrentUrl',
            'reurbano_date'              => 'formatDate'
        );

        $functions = array();
        foreach($mappings as $twigFunction => $method) {
            $functions[$twigFunction] = new Twig_Function_Method($this, $method, array('is_safe' => array('html')));
        }

        return $functions;
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        $filters = array(
            // formatting filters
            'date'    => new Twig_Filter_Method($this, 'formatDate'),
        );

        return $filters;
    }

    public function formatDate($date, $format = null)
    {
        if (!$date instanceof DateTime) {
            $date = new DateTime((ctype_digit($date) ? '@' : '').$date);
        }
        if ($format) {
            return $date->format($format);
        }
        if (null === $this->dateFormatter) {
            $this->dateFormatter = new IntlDateFormatter(
                $this->container->get('session')->getLocale(),
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::SHORT
            );
        }

        // for compatibility with PHP 5.3.3
        $date = $date->getTimestamp();

        return $this->dateFormatter->format($date);
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    protected function autoLink($text)
    {
        return preg_replace_callback('~
            (                       # leading text
                <\w+.*?>|             #   leading HTML tag, or
                [^=!:\'"/]|           #   leading punctuation, or
                ^                     #   beginning of line
            )
            (
                (?:https?://)|        # protocol spec, or
                (?:www\.)             # www.*
            )
            (
                [-\w]+                   # subdomain or domain
                (?:\.[-\w]+)*            # remaining subdomains or domain
                (?::\d+)?                # port
                (?:/(?:(?:[\~\w\+%-\@]|(?:[,.;:][^\s$]))+)?)* # path
                (?:\?[\w\+%&=.;-]+)?     # query string
                (?:\#[\w\-]*)?           # trailing anchor
            )
            ([[:punct:]]|\s|<|$)    # trailing text
            ~x',
            function($matches)
            {
                if (preg_match("/<a\s/i", $matches[1]))
                {
                    return $matches[0];
                }
                else
                {
                    return $matches[1].'<a href="'.($matches[2] == 'www.' ? 'http://www.' : $matches[2]).$matches[3].'" target="_blank">'.$matches[2].$matches[3].'</a>'.$matches[4];
                }
            },
            $text
        );
    }

    public function userText($text)
    {
        return nl2br($this->autoLink($this->escape($text)));
    }

    public function shorten($text, $length = 140)
    {
        return mb_substr(str_replace("\n", ' ', $this->escape($text)), 0, $length);
    }

    public function getCurrentUrl()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'http://test/';
    }

    public function getSession($key, $default = null)
    {
        return $this->container->get('session')->get($key, $default);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'reurbano';
    }

}
