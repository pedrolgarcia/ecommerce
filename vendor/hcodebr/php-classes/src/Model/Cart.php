<?php

    namespace Hcode\Model;

    use \Hcode\DB\Sql;
    use \Hcode\Model;
    use \Hcode\Mailer;
    use \Hcode\Model\User;

    class Cart extends Model {

        const SESSION = "Cart";

        public static function getFromSession()
        {
            $cart = new Cart();

            if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]["idcart"] > 0)
            {
                $cart->get((int)$_SESSION[Cart::SESSION]["idcart"]);
            } 
            else 
            {
                $cart->getFromSessionId();
                
                if(!(int)$cart->getidcart() > 0)
                {
                    $data = array(
                        "dessessionid"=>session_id()
                    );

                    if(User::checkLogin(false))
                    {
                        $user = User::getFromSession();
                        $data["iduser"] = $user->getiduser();
                    }

                    $cart->setData($data);
                    $cart->save();
                    $cart->setToSession();
                }
            }
        }

        public function setToSession()
        {
            $_SESSION[Cart::SESSION] = $this->getValues();
        }

        public function get(int $idcart)
        {
            $sql = new Sql();
            $res = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", array(
                ":idcart"=>$idcart
            ));

            if(count($res) > 0)
            {
                $this->setData($res[0]);
            }
        }

        public function getFromSessionId()
        {
            $sql = new Sql();
            $res = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", array(
                ":dessessionid"=>session_id()
            ));

            if(count($res) > 0)
            {
                $this->setData($res[0]);
            }
        }

        public function save()
        {
            $sql = new Sql();
            $res = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)", array(
                ":idcart"=>$this->getidcart(),
                ":dessessionid"=>$this->getdessessionid(),
                ":iduser"=>$this->getiduser(),
                ":deszipcode"=>$this->getdeszipcode(),
                ":vlfreight"=>$this->gevlfreight(),
                ":nrdays"=>$this->getnrdays(),
            ));

            $this->setData($res[0]);

        }
    }

?>