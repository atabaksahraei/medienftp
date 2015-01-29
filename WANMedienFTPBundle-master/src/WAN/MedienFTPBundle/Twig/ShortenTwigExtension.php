<?php

namespace WAN\MedienFTPBundle\Twig;

class ShortenTwigExtension extends \Twig_Extension
{
    protected $replacement = "…"; // = Ellipse (strlen ist 3, obwohl nur ein Zeichen)

    public function getFilters()
    {
        $filters = array();
        $filters['shortenTo'] = new \Twig_Filter_Method($this, 'shortenTo');
        $filters['shortenFilenameTo'] = new \Twig_Filter_Method($this, 'shortenFilenameTo');
        $filters['shortenPathTo'] = new \Twig_Filter_Method($this, 'shortenPathTo');
        $filters['basename'] = new \Twig_Filter_Method($this, 'basename');

        return $filters;
    }

    /** kürzt folgendermaßen:
     *  shorten_to("formelsammlung.pdf", $maxLen)
     *
     * maxLen 0:    …
     *  * maxLen 1:    …
     * maxLen 2:    …
     * maxLen 3:    …
     * maxLen 4:    f…
     * maxLen 5:    fo…
     * maxLen 6:    for…
     * maxLen 7:    form…
     * maxLen 8:    forme…
     * maxLen 9:    formel…
     * maxLen 10:    formels…
     * maxLen 11:    formelsa…
     * maxLen 12:    formelsam…
     * maxLen 13:    formelsamm…
     * maxLen 14:    formelsamml…
     * maxLen 15:    formelsammlu…
     * maxLen 16:    formelsammlun…
     * maxLen 17:    formelsammlung…
     * maxLen 18:    formelsammlung.pdf
     *
     * Priorität: keine; von vorne beginnend aufgefüllt
     *
     */
    public function shortenTo($path, $maxLen)
    {
        if (strlen($path) <= $maxLen) {
            return $path;
        }

        if ($maxLen < strlen($this->replacement)) {
            return $this->replacement;
        }

        $res = substr($path, 0, $maxLen - strlen($this->replacement));
        $res .= $this->replacement;

        return $res;
    }

    /** kürzt folgendermaßen:
     *  shorten_path_to("1. Semester/PG/formelsammlung.pdf", $maxLen)
     *
     * maxLen 0:    erg: "…"
     * maxLen 1:    erg: "…"
     * maxLen 2:    erg: "…"
     * maxLen 3:    erg: "…"
     * maxLen 4:    erg: "…f"
     * maxLen 5:    erg: "…df"
     * maxLen 6:    erg: "…pdf"
     * maxLen 7:    erg: "….pdf"
     * maxLen 8:    erg: "…g.pdf"
     * maxLen 9:    erg: "…ng.pdf"
     * maxLen 10:    erg: "…ung.pdf"
     * maxLen 11:    erg: "…lung.pdf"
     * maxLen 12:    erg: "…mlung.pdf"
     * maxLen 13:    erg: "…mmlung.pdf"
     * maxLen 14:    erg: "…ammlung.pdf"
     * maxLen 15:    erg: "…sammlung.pdf"
     * maxLen 16:    erg: "…lsammlung.pdf"
     * maxLen 17:    erg: "…elsammlung.pdf"
     * maxLen 18:    erg: "…melsammlung.pdf"
     * maxLen 19:    erg: "…rmelsammlung.pdf"
     * maxLen 20:    erg: "…ormelsammlung.pdf"
     * maxLen 21:    erg: "…formelsammlung.pdf"
     * maxLen 22:    erg: "1…formelsammlung.pdf"
     * maxLen 23:    erg: "1.…formelsammlung.pdf"
     * maxLen 24:    erg: "1. …formelsammlung.pdf"
     * maxLen 25:    erg: "1. S…formelsammlung.pdf"
     * maxLen 26:    erg: "1. Se…formelsammlung.pdf"
     * maxLen 27:    erg: "1. Sem…formelsammlung.pdf"
     * maxLen 28:    erg: "1. Seme…formelsammlung.pdf"
     * maxLen 29:    erg: "1. Semes…formelsammlung.pdf"
     * maxLen 30:    erg: "1. Semest…formelsammlung.pdf"
     * maxLen 31:    erg: "1. Semeste…formelsammlung.pdf"
     * maxLen 32:    erg: "1. Semester…formelsammlung.pdf"
     * maxLen 33:    erg: "1. Semester/PG/formelsammlung.pdf"
     *
     * Priorität: Dateiname mit Endung, dann restlicher Pfad am Anfang beginnend
     */
    public function shortenPathTo($path, $maxLen)
    {
        if (strlen($path) <= $maxLen) {
            return $path;
        }

        $this->replacement = "…"; // = Ellipse (strlen ist 3, obwohl nur ein Zeichen)
        $base = $this->mb_pathinfo($path, PATHINFO_BASENAME);
        $dir = $this->mb_pathinfo($path, PATHINFO_DIRNAME);
        if (strlen($base) + strlen(
            $this->replacement
        ) >= $maxLen
        ) // falls Dateiname alleine länger oder gleich als $maxLen
        {
            return $this->replacement . substr($base, strlen($base) + strlen($this->replacement) - $maxLen);
        }

        $res = $this->replacement . $base;
        // echo "<pre>res: ".strlen($res)."<br />base: ".strlen($base)."<br />dir: ".strlen($dir)."</pre>";
        $res = substr($dir, 0, $maxLen - strlen($res)) . $res;

        return $res;
    }

    /**
     * kürzt folgendermaßen:
     * shorten_filename_to("formelsammlung.pdf", $maxLen)
     *
     * maxLen 0:    …pdf
     * maxLen 1:    …pdf
     * maxLen 2:    …pdf
     * maxLen 3:    …pdf
     * maxLen 4:    …pdf
     * maxLen 5:    …pdf
     * maxLen 6:    …pdf
     * maxLen 7:    f…pdf
     * maxLen 8:    fo…pdf
     * maxLen 9:    for…pdf
     * maxLen 10:    form…pdf
     * maxLen 11:    forme…pdf
     * maxLen 12:    formel…pdf
     * maxLen 13:    formels…pdf
     * maxLen 14:    formelsa…pdf
     * maxLen 15:    formelsam…pdf
     * maxLen 16:    formelsamm…pdf
     * maxLen 17:    formelsamml…pdf
     * maxLen 18:    formelsammlung.pdf
     *
     * Priorität: Endung immer anzeigen, danach Dateiname von vorne beginnend
     */
    public function shortenFilenameTo($filename, $maxLen)
    {
        $ext = $this->mb_pathinfo($filename, PATHINFO_EXTENSION);

        if (strlen($filename) <= $maxLen) {
            return $filename;
        }

        if ($maxLen < 2 * strlen($this->replacement)) {
            return $this->replacement . $ext;
        }

        $res = substr($filename, 0, $maxLen - strlen($this->replacement) - strlen($ext));
        $res .= $this->replacement;
        $res .= $ext;

        return $res;
    }

    public function basename($path)
    {
        return $this->mb_pathinfo($path, PATHINFO_BASENAME);
    }

    public function mb_pathinfo($path, $options = null)
    {
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $m);

        if ($options !== null) {
            if ($options == PATHINFO_DIRNAME) {
                return $m[1];
            } elseif ($options == PATHINFO_BASENAME) {
                return $m[2];
            } elseif ($options == PATHINFO_EXTENSION) {
                if (isset($m[5])) {
                    return $m[5];
                } else {
                    return null;
                }
            } elseif ($options == PATHINFO_FILENAME) {
                return $m[3];
            }
        }

        if ($m[1]) {
            $ret['dirname'] = $m[1];
        }
        if ($m[2]) {
            $ret['basename'] = $m[2];
        }
        if ($m[5]) {
            $ret['extension'] = $m[5];
        }
        if ($m[3]) {
            $ret['filename'] = $m[3];
        }

        return $ret;
    }

    public function getName()
    {
        return 'shorten_twig_extension';
    }
}
