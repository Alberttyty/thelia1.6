<?php
include_once realpath(dirname(__FILE__)) . '/../../../classes/PluginsPaiements.class.php';
include_once realpath(dirname(__FILE__)) . '/../../../classes/Variable.class.php';

/**
 *
 * Class paybox permettant de gérer les requêtes envoyés à paybox
 *
 * @author Manuel Raynaud <mraynaud@openstudio.fr>
 */

class Paybox extends PluginsPaiements
{
    public $defalqcmd = 0;

    public $id;
    public $key;
    public $value;
    public $description;
    public $hidden;

    public $bddvars = array('id','key','value','description','hidden');

    const TABLE = 'paybox';
    const PBX_HMAC = 'PBX_HMAC';
    const PBX_SECRET = 'PBX_SECRET';

    public $table = self::TABLE;

    protected $values = array();
    protected $isHashed = false;

    function __construct()
    {
        parent::__construct("Paybox");
    }

    public function init()
    {
        $this->ajout_desc("CB", "CB", "", 1);
        $query = 'CREATE TABLE IF NOT EXISTS `paybox` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `key` varchar(255) NOT NULL,
            `value` varchar(255) NOT NULL,
            `description` varchar(255) NOT NULL,
            `hidden` tinyint(4) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `key` (`key`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;'
        ;
        $this->query($query);

        //insert default values

        //PBX_SITE
        $site  = new self;
        $site->key = 'PBX_SITE';
        $site->description = 'Numéro du site';
        $site->hidden = 0;
        $site->add();

        //PBX_RANG
        $rang = new self;
        $rang->key = 'PBX_RANG';
        $rang->description = 'Numéro de rang';
        $rang->hidden = 0;
        $rang->add();

        //PBX_IDENTIFIANT
        $identifiant = new self;
        $identifiant->key = 'PBX_IDENTIFIANT';
        $identifiant->description = 'Identifiant interne';
        $identifiant->hidden = 0;
        $identifiant->add();

        //RETOUR
        $retour = new self;
        $retour->key = 'PBX_RETOUR';
        $retour->value = 'montant:M;ref:R;auto:A;trans:T;erreur:E;sign:K';
        $retour->description = 'Liste des variables à retourner par paybox';
        $retour->hidden = 0;
        $retour->add();

        //ALGO HASH
        $hashs = array(
            'sha512',
            'sha256',
            'sha384',
            'ripemd160',
            'sha224',
            'mdc2'
        );

        $hashEnabled = hash_algos();

        foreach($hashs as $hash){
            if(in_array($hash, $hashEnabled)){
                $algoHash = $hash;
                break;
            }
        }

        $algo = new self;
        $algo->key = 'PBX_HASH';
        $algo->value = $algoHash;
        $algo->description = 'algorithme de hachage des paramètres';
        $algo->hidden = 0;
        $algo->add();

        //SECRET KEY
        $secret = new self;
        $secret->key = self::PBX_SECRET;
        $secret->description = 'clé privé d\'échange';
        $secret->hidden = 1;
        $secret->add();

        //ABANDON
        $abandon = new self;
        $abandon->key = 'PBX_ANNULE';
        $abandon->value = rtrim(Variable::lire('urlsite'), '/').'/?fond=regret';
        $abandon->description = 'Url de retour en cas d\'abandon';
        $abandon->hidden = 0;
        $abandon->add();

        //SUCCESS
        $succes = new self;
        $succes->key = 'PBX_EFFECTUE';
        $succes->value = rtrim(Variable::lire('urlsite'), '/').'/?fond=merci';
        $succes->description = 'Url de retour en cas de succès';
        $succes->hidden = 0;
        $succes->add();

        //REFUS
        $refus = new self;
        $refus->key = 'PBX_REFUSE';
        $refus->value = rtrim(Variable::lire('urlsite'), '/').'/?fond=regret';
        $refus->description = 'URL de retour en cas de refus du paiement';
        $refus->hidden = 0;
        $refus->add();

        //URL IPN
        $ipn = new self;
        $ipn->key = 'PBX_REPONDRE_A';
        $ipn->value = rtrim(Variable::lire('urlsite'), '/').'/client/plugins/paybox/confirmation.php';
        $ipn->description = 'Url Ipn';
        $ipn->hidden = 0;
        $ipn->add();
    }

    /**
     *
     * Find in paybox table all value to send to paypbox server and load the values in $values array
     *
     * @return \Paybox
     *
     */
    public function loadValues()
    {
        $query = 'select `key`, `value` from `'.$this->table.'` where hidden=0';

        $handle = $this->query($query);

        while($row = $this->fetch_object($handle)){
            $this->addValue($row->key, $row->value);
        }

        $this->isHashed = false;

        return $this;
    }

    /**
     *
     * add couple key/value in $values array.
     *
     * @param string $key
     * @param \Paybox return Paybox instance for chaining method
     */
    public function addValue($key, $value)
    {
        if($key == 'PBX_HASH') $value = strtoupper($value);

        $this->values[$key] = $value;
        $this->isHashed = false;

        return $this;
    }

    /**
     *
     * add an array of couple key/value in $values array. This couples are sending to paybox.
     *
     * @param array $values
     * @return \Paybox
     */
    public function addValues(array $values)
    {
        foreach($values as $key => $value){
            $this->addValue($key, $value);
        }

        return $this;
    }

    /**
     *
     * @param string $key
     * @return int 0 or 1
     */
    public function charger($key = null, $var2 = null)
    {
        if ($key != null) return $this->getVars('SELECT * FROM  `'.$this->table.'` WHERE  `key`="'.$key.'"');
    }

    /**
     * static function allowing to know directly the value of a specific key.
     *
     *
     * @param string $key
     * @return string
     */
    public static function lire($key)
    {
        $self = new self;
        $self->charger($key);
        return $self->value;
    }

    /**
     *
     *
     *
     */
    public function hashParam()
    {
        if(isset($this->values[self::PBX_HMAC])){
            unset($this->values[self::PBX_HMAC]);
        }

        foreach($this->values as $key => $value){
            $param .= "&".$key.'='.$value;
        }

        $param = ltrim($param,'&');

        $binkey = pack('H*', self::lire(self::PBX_SECRET));

        $this->values[self::PBX_HMAC] = strtoupper(hash_hmac($this->values['PBX_HASH'], $param, $binkey));
        //$this->values[self::PBX_HMAC] = strtoupper(hash_hmac($this->values['PBX_HASH'], $param, self::lire(self::PBX_SECRET)));
        $this->isHashed = true;
    }

    /**
     *
     * @return Array return the array $values parameter
     */
    public function getValues()
    {
        if($this->isHashed === false) $this->hashParam();
        return $this->values;
    }

    /**
     *
     * search the key in $values array and return the value if exists. Else return $default param
     *
     * @param string $key
     * @param string $default
     * @return string default value
     */
    public function getValue($key, $default = null)
    {
        return isset($this->values[$key]) ? $this->values[$key] : $default;
    }

    /**
     *
     * calculate hash of paramters send to paybox
     *
     * @return string
     */
    public function getHash()
    {
        if($this->isHashed === false) $this->hashParam();
        return $this->values[self::PBX_HMAC];
    }

    public function charger_admin(array $exclude = null)
    {
        $query_exclude = '';

        if(!is_null($exclude)){
            $param_exclude = '';
            foreach($exclude as $key){
                $param_exclude .= ' "'.$key.'",';
            }

            $param_exclude = rtrim($param_exclude, ',');
            $query_exclude = ' and `key` not in ('.$param_exclude.')';
        }

        $query = 'select * from `'.$this->table.'` where 1'.$query_exclude;
        $handle = $this->query($query);

        $result = array();

        while($row = $this->fetch_object($handle)){
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Modify all parameters from admin interface
     */
    public function modifyAll()
    {
        foreach($_POST as $key => $value){
            if(strpos($key, 'value_') !== false){
                $key = str_replace('value_','',$key);
                $param = new self;
                if($param->charger($key)){
                    $param->value = $value;
                    $param->maj();
                }
            }
        }
    }

    function paiement($commande)
    {
        header("Location: " . "client/plugins/paybox/paiement.php"); exit;
    }

    function confirmation($commande)
    {
        $module = new Modules();
        $module->charger_id($commande->paiement);
        if ($module->nom==$this->getNom()){
        		if ($commande->statut == 2){
            		parent::mail($commande);
            		//mail('mathieu@pixel-plurimedia.fr', 'Test Paybox', $commande->ref);
            		//modules_fonction("statut", $commande);
          	}
        }
    }
}
?>
