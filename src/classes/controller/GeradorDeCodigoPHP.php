<?php

/**
 * 
 * @author jefferson
 *
 */
class GeradorDeCodigoPHP extends GeradorDeCodigo
{

    /**
     * retorna um array de objetos do tipo GeradorDeCodigoPHP
     * Cada estrutura representa um arquivo de uma classe do software em questao.
     * 
     * @param Software $software
     * @return multitype:GeradorDeCodigoPHP |NULL
     */
    public static function geraClasses(Software $software)
    {
        $listaDeObjetos = $software->getObjetos();
        if ($listaDeObjetos) {
            $geradores = array();
            foreach ($listaDeObjetos as $objeto) {
                
                $nomedosite = $software->getNome();
                $gerador = GeradorDeCodigoPHP::geraCodigoDeObjeto($objeto, $nomedosite);
                $geradores[] = $gerador;
            }
        }
        if (isset($geradores)) {
            return $geradores;
        } else {
            return null;
        }
    }

    /**
     * retorna um array de objetos do tipo GeradorDeCodigoPHP
     * Cada estrutura representa um arquivo de uma classe do software em questao.
     * 
     * @param Software $software
     * @return multitype:GeradorDeCodigoPHP |NULL
     */
    public static function geraClassesController(Software $software)
    {
        $listaDeObjetos = $software->getObjetos();
        if ($listaDeObjetos) {
            $geradores = array();
            foreach ($listaDeObjetos as $objeto) {
                
                $nomedosite = $software->getNome();
                $gerador = GeradorDeCodigoPHP::geraCodigoDeController($objeto, $nomedosite);
                $geradores[] = $gerador;
            }
        }
        if (isset($geradores)) {
            return $geradores;
        } else {
            return null;
        }
    }

    public static function geraFormularios(Software $software)
    {
        $listaDeObjetos = $software->getObjetos();
        if ($listaDeObjetos) {
            $geradores = array();
            foreach ($listaDeObjetos as $objeto) {
                $gerador = GeradorDeCodigoPHP::geraForm($objeto, $software);
                $geradores[] = $gerador;
            }
        }
        if (isset($geradores)) {
            return $geradores;
        } else {
            return null;
        }
    }

    /**
     * Retorna uma estrutura que representa o codigo e o caminho de cada
     * Objeto responsÃ¡vel por insersao de objetos no banco de dados.
     * 
     * @param Software $software
     * @return GeradorDeCodigoPHP|NULL
     */
    public static function geraDaos(Software $software)
    {
        $listaDeObjetos = $software->getObjetos();
        if ($listaDeObjetos) {
            $geradores = array();
            foreach ($listaDeObjetos as $objeto) {
                
                // Gera o codigo de cada objeto
                // Gera o nome do arquivo
                $nomedosite = $software->getNome();
                
                // instancia no geradorDePHP
                // Armazena em Um vetor.
                $gerador = GeradorDeCodigoPHP::geraCodigoDeObjetoDAO($objeto, $nomedosite);
                
                $geradores[] = $gerador;
            }
        }
        if (isset($geradores)) {
            return $geradores;
        } else {
            return null;
        }
    }

    public static function geraClasseDao(Software $software)
    {
        $nomeDoSite = $software->getNome();
        
        $codigo = '<?php


class DAO {
	const ARQUIVO_CONFIGURACAO = "../' . strtolower($software->getNome()) . '_bd.ini";
	
	protected $conexao;
	private $tipoDeConexao;
	private $sgdb;

	public function getSgdb(){
		return $this->sgdb;
	}
	public function DAO(PDO $conexao = null) {
		if ($conexao != null) {
			$this->conexao = $conexao;
		} else {
			
			$this->fazerConexao ();
		}
	}
	public function getEntidadeUsuarios(){
		return $this->entidadeUsuarios;
	}
	
	
	public function fazerConexao() {
		$config = parse_ini_file ( self::ARQUIVO_CONFIGURACAO );
		$bd = array();
		$bd [\'sgdb\'] = $config [\'sgdb\'];
		$bd [\'bd_nome\'] = $config [\'bd_nome\'];
		$bd [\'host\'] = $config [\'host\'];
		$bd [\'porta\'] = $config [\'porta\'];
		$bd [\'usuario\'] = $config [\'usuario\'];
		$bd [\'senha\'] = $config [\'senha\'];

		if ($bd [\'sgdb\'] == "postgres") {
			$this->conexao = new PDO ( \'pgsql:host=\' . $bd [\'host\'] . \' dbname=\' . $bd [\'bd_nome\'] . \' user=\' . $bd [\'usuario\'] . \' password=\' . $bd [\'senha\'] );
		} else if ($bd [\'sgdb\'] == "mssql") {
			$this->conexao = new PDO ( \'dblib:host=\' . $bd [\'host\'] . \';dbname=\' . $bd [\'bd_nome\'], $bd [\'usuario\'], $bd [\'senha\'] );
			
		}else if($bd[\'sgdb\'] == "mysql"){
			$this->conexao = new PDO( \'mysql:host=\' . $bd [\'host\'] . \';dbname=\' .  $bd [\'bd_nome\'], $bd [\'usuario\'], $bd [\'senha\']);
		}else if($bd[\'sgdb\']== "sqlite"){
			$this->conexao = new PDO(\'sqlite:\'.$bd [\'bd_nome\']);
		}
		$this->sgdb = $bd[\'sgdb\'];
	}
	public function setConexao($conexao) {
		$this->conexao = $conexao;
	}
	public function getConexao() {
		return $this->conexao;
	}
	public function fechaConexao() {
		$this->conexao = null;
	}
	public function getTipoDeConexao() {
		return $this->tipoDeConexao;
	}
	public function setTipoDeConexao($tipo) {
		$this->tipoDeConexao = $tipo;
	}
}

?>
		';
        
        $gerador = new GeradorDeCodigoPHP();
        $gerador->codigo = $codigo;
        $gerador->caminho = 'sistemasphp/' . $nomeDoSite . '/src/classes/dao/DAO.php';
        return $gerador;
    }

    /**
     *
     * Gera códigos das classes do pacote DAO
     * 
     * @param Objeto $objeto
     * @param String $nomeDoSite
     * @return GeradorDeCodigoPHP
     */
    public static function geraCodigoDeObjetoDAO(Objeto $objeto, $nomeDoSite)
    {
        $nomeDoObjeto = strtolower($objeto->getNome());
        $nomeDoObjetoMA = strtoupper(substr($objeto->getNome(), 0, 1)) . substr($objeto->getNome(), 1, 100);
        $nomeDoObjetoDAO = strtoupper(substr($objeto->getNome(), 0, 1)) . substr($objeto->getNome(), 1, 100) . 'DAO';
        
        $codigo = '<?php
		
/**
 * Classe feita para manipulação do objeto ' . $nomeDoObjetoMA . '
 * feita automaticamente com programa gerador de software inventado por
 * @author Jefferson Uchôa Ponte
 *
 *
 */
class ' . $nomeDoObjetoDAO . ' extends DAO {
	

    public function atualizar('.$nomeDoObjetoMA.' $'.$nomeDoObjeto.')
    {

        $id = $'.$nomeDoObjeto.'->get'.ucfirst ($objeto->getAtributos()[0]->getNome()).'();
        $sql = "UPDATE '.$nomeDoObjeto.' 
                SET
                ';
        $listaAtributo = array();
        foreach ($objeto->getAtributos() as $atributo) {
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            if(substr($atributo->getTipo(), 0, 6) == 'Array '){
                continue;
            }
            $listaAtributo[] = $atributo;
        }
        $i = 0;
        foreach ($listaAtributo as $atributo) {
            $i ++;
            $codigo .= $atributo->getNome().' = :'.$atributo->getNome();
            if ($i != count($listaAtributo)) {
                $codigo .= ', 
                ';
            }
        }
        $codigo .= '
                WHERE '.$nomeDoObjeto.'.id = :id;";';
        
        
        foreach ($listaAtributo as $atributo) {
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $nomeDoAtributoMA = strtoupper(substr($atributo->getNome(), 0, 1)) . substr($atributo->getNome(), 1, 100);
            $codigo .= '
			$' . $atributo->getNome() . ' = $' . $nomeDoObjeto . '->get' . $nomeDoAtributoMA . '();';
        }
        $codigo .= '

        try {
            
            $stmt = $this->getConexao()->prepare($sql);';
        foreach ($objeto->getAtributos() as $atributo) {
            if(substr($atributo->getTipo(), 0, 6) == 'Array '){
                continue;
            }
            $codigo .= '
			$stmt->bindParam("' . $atributo->getNome() . '", $' . $atributo->getNome() . ', PDO::PARAM_STR);';
        }
        
        $codigo .= '

            return $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();   
        }

    }
	
	public function inserir(' . $nomeDoObjetoMA . ' $' . $nomeDoObjeto . '){
		
		$sql = "INSERT INTO ' . $nomeDoObjeto . '(';
        $i = 0;
        foreach ($objeto->getAtributos() as $atributo) {
            $i ++;
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $codigo .= $atributo->getNome();
            if ($i != count($objeto->getAtributos())) {
                $codigo .= ', ';
            }
        }
        $codigo .= ')
				VALUES(';
        $i = 0;
        foreach ($objeto->getAtributos() as $atributo) {
            $i ++;
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $codigo .= ':' . $atributo->getNome();
            if ($i != count($objeto->getAtributos())) {
                $codigo .= ', ';
            }
        }
        
        $codigo .= ')";';
        foreach ($objeto->getAtributos() as $atributo) {
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $nomeDoAtributoMA = strtoupper(substr($atributo->getNome(), 0, 1)) . substr($atributo->getNome(), 1, 100);
            $codigo .= '
			$' . $atributo->getNome() . ' = $' . $nomeDoObjeto . '->get' . $nomeDoAtributoMA . '();';
        }
        
        $codigo .= '
		try {
			$db = $this->getConexao();
			$stmt = $db->prepare($sql);';
        foreach ($objeto->getAtributos() as $atributo) {
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $codigo .= '		
			$stmt->bindParam("' . $atributo->getNome() . '", $' . $atributo->getNome() . ', PDO::PARAM_STR);';
        }
        
        $codigo .= '
			return $stmt->execute();
		} catch(PDOException $e) {
			echo \'{"error":{"text":\'. $e->getMessage() .\'}}\';
		}
	}
	public function excluir(' . $nomeDoObjetoMA . ' $' . $nomeDoObjeto . '){
		$' . $objeto->getAtributos()[0]->getNome() . ' = $' . $nomeDoObjeto . '->get' . strtoupper(substr($objeto->getAtributos()[0]->getNome(), 0, 1)) . substr($objeto->getAtributos()[0]->getNome(), 1, 100) . '();
		$sql = "DELETE FROM ' . $nomeDoObjeto . ' WHERE ' . $objeto->getAtributos()[0]->getNome() . ' = :' . $objeto->getAtributos()[0]->getNome() . '";
		
		try {
			$db = $this->getConexao();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("' . $objeto->getAtributos()[0]->getNome() . '", $' . $objeto->getAtributos()[0]->getNome() . ', PDO::PARAM_INT);
			return $stmt->execute();
	
		} catch(PDOException $e) {
			echo \'{"error":{"text":\'. $e->getMessage() .\'}}\';
		}
	}

	
	public function retornaLista() {
		$lista = array ();
		$sql = "SELECT * FROM ' . $nomeDoObjeto . ' LIMIT 1000";
		$result = $this->getConexao ()->query ( $sql );
	
		foreach ( $result as $linha ) {
				
			$' . $nomeDoObjeto . ' = new ' . $nomeDoObjetoMA . '();
        ';
        
        foreach ($objeto->getAtributos() as $atributo) {
            $nomeDoAtributoMA = strtoupper(substr($atributo->getNome(), 0, 1)) . substr($atributo->getNome(), 1, 100);
            
            $codigo .= '
			$' . $nomeDoObjeto . '->set' . $nomeDoAtributoMA . '( $linha [\'' . $atributo->getNome() . '\'] );';
        }
        $codigo .= '
			$lista [] = $' . $nomeDoObjeto . ';
		}
		return $lista;
	}';


        
        
        
        
        foreach ($objeto->getAtributos() as $atributo) {

                $nomeDoAtributoMA = strtoupper(substr($atributo->getNome(), 0, 1)) . substr($atributo->getNome(), 1, 100);
                $id = $atributo->getNome();
                $codigo .= '

    public function pesquisaPor'.$nomeDoAtributoMA.'(' . $nomeDoObjetoMA . ' $' . $nomeDoObjeto . ') {
        $lista = array();
	    $'.$id.' = $'.$nomeDoObjeto.'->get'.$nomeDoAtributoMA.'();
	    $sql = "SELECT * FROM ' . $nomeDoObjeto . ' WHERE '.$id.' like \'%$'.$id.'%\'";
	    $result = $this->getConexao ()->query ( $sql );
	        
	    foreach ( $result as $linha ) {';
                foreach ($objeto->getAtributos() as $atributo2) {
                    
                    $nomeDoAtributoMA = strtoupper(substr($atributo2->getNome(), 0, 1)) . substr($atributo2->getNome(), 1, 100);
                    $codigo .= '
	        $'.$nomeDoObjeto.'->set'.$nomeDoAtributoMA.'( $linha [\''.$atributo2->getNome().'\'] );';
                    
                }
                $codigo .= '
			$lista [] = $' . $nomeDoObjeto . ';
		}
		return $lista;
	}';
                
               
        }
        
 $codigo .= '
		
				
}';
        
        $gerador = new GeradorDeCodigoPHP();
        $gerador->codigo = $codigo;
        $gerador->caminho = 'sistemasphp/' . $nomeDoSite . '/src/classes/dao/' . $nomeDoObjetoDAO . '.php';
        return $gerador;
    }

    /**
     *
     * @param Objeto $objeto
     * @return GeradorDeCodigoPHP
     */
    public static function geraCodigoDeController(Objeto $objeto, $nomeDoSite)
    {
        $geradorDeCodigo = new GeradorDeCodigoPHP();
        $nomeDoObjeto = strtolower($objeto->getNome());
        $nomeDoObjetoMa = strtoupper(substr($objeto->getNome(), 0, 1)) . substr($objeto->getNome(), 1, 100);
        
        $codigo = '<?php	

/**
 * Classe feita para manipulação do objeto ' . $nomeDoObjetoMa . '
 * feita automaticamente com programa gerador de software inventado por
 * @author Jefferson Uchôa Ponte <j.pontee@gmail.com>
 */
class ' . $nomeDoObjetoMa . 'Controller {
	private $post;
	private $view;
    private $dao;

    public static function main(){
        $controller = new '.$nomeDoObjetoMa.'Controller();
        if (!(isset($_GET[\'cadastrar\']) || isset($_GET[\'selecionar\']) || isset($_GET[\'editar\']) || isset($_GET[\'deletar\']) )){
            $controller->listar();
        }
        $controller->cadastrar();
        $controller->selecionar();
        $controller->editar();
        $controller->deletar();
    }
	public function __construct(){
		$this->dao = new ' . $nomeDoObjetoMa . 'DAO();
		$this->view = new ' . $nomeDoObjetoMa . 'View();
		foreach($_POST as $chave => $valor){
			$this->post[$chave] = $valor;
		}
	}
	public function listar() {
		$' . $nomeDoObjeto . 'Dao = new ' . $nomeDoObjetoMa . 'DAO ();
		$lista = $' . $nomeDoObjeto . 'Dao->retornaLista ();
		$this->view->exibirLista($lista);
	}			
    public function selecionar(){
	    if(!isset($_GET[\'selecionar\'])){
	        return;
	    }
        $selecionado = new '.$nomeDoObjetoMa.'();
	    $selecionado->set'.ucfirst ($objeto->getAtributos()[0]->getNome()).'($_GET[\'selecionar\']);
	    $this->dao->pesquisaPor'.ucfirst ($objeto->getAtributos()[0]->getNome()).'($selecionado);
	    $this->view->mostrarSelecionado($selecionado);
    }
	public function cadastrar() {
        if(!isset($_GET[\'cadastrar\'])){
            return;
        }
		
        if(!isset($this->post[\'enviar_' . $nomeDoObjeto . '\'])){
            $this->view->mostraFormInserir();   
		    return;
		}
		if (! ( ';
        $i = 0;
        foreach ($objeto->getAtributos() as $atributo) {
            $i ++;
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $codigo .= 'isset ( $this->post [\'' . $atributo->getNome() . '\'] )';
            if ($i != count($objeto->getAtributos())) {
                $codigo .= ' && ';
            }
        }
        
        $codigo .= ')) {
			echo "Incompleto";
			return;
		}
	
		$' . $nomeDoObjeto . ' = new ' . $nomeDoObjetoMa . ' ();';
        foreach ($objeto->getAtributos() as $atributo) {
            $nomeDoAtributoMA = strtoupper(substr($atributo->getNome(), 0, 1)) . substr($atributo->getNome(), 1, 100);
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $codigo .= '		
		$' . $nomeDoObjeto . '->set' . $nomeDoAtributoMA . ' ( $this->post [\'' . $atributo->getNome() . '\'] );';
        }
        
        $codigo .= '	
		
		if ($this->dao->inserir ( $' . $nomeDoObjeto . ' )) 
        {
			echo "Sucesso";
		} else {
			echo "Fracasso";
		}
        echo \'<META HTTP-EQUIV="REFRESH" CONTENT="0; URL=index.php?pagina=' . $nomeDoObjeto . '">\';
	}
    public function editar(){
	    if(!isset($_GET[\'editar\'])){
	        return;
	    }
        $selecionado = new '.$nomeDoObjetoMa.'();
	    $selecionado->set'.ucfirst ($objeto->getAtributos()[0]->getNome()).'($_GET[\'editar\']);
	    $this->dao->pesquisaPor'.ucfirst ($objeto->getAtributos()[0]->getNome()).'($selecionado);
	    
        if(!isset($_POST[\'editar_' . $nomeDoObjeto . '\'])){
            $this->view->mostraFormEditar($selecionado);
            return;
        }

		if (! ( ';
        $i = 0;
        foreach ($objeto->getAtributos() as $atributo) {
            $i ++;
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $codigo .= 'isset ( $this->post [\'' . $atributo->getNome() . '\'] )';
            if ($i != count($objeto->getAtributos())) {
                $codigo .= ' && ';
            }
        }
        
        $codigo .= ')) {
			echo "Incompleto";
			return;
		}
';
        foreach ($objeto->getAtributos() as $atributo) {
            $nomeDoAtributoMA = strtoupper(substr($atributo->getNome(), 0, 1)) . substr($atributo->getNome(), 1, 100);
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $codigo .= '		
		$selecionado->set' . $nomeDoAtributoMA . ' ( $this->post [\'' . $atributo->getNome() . '\'] );';
        }
        
        $codigo .= '	
		
		if ($this->dao->atualizar ($selecionado )) 
        {

			echo "Sucesso";
		} else {
			echo "Fracasso";
		}
        echo \'<META HTTP-EQUIV="REFRESH" CONTENT="3; URL=index.php?pagina=' . $nomeDoObjeto . '">\';

    }
    public function deletar(){
	    if(!isset($_GET[\'deletar\'])){
	        return;
	    }
        $selecionado = new '.$nomeDoObjetoMa.'();
	    $selecionado->set'.ucfirst ($objeto->getAtributos()[0]->getNome()).'($_GET[\'deletar\']);
	    $this->dao->pesquisaPor'.ucfirst ($objeto->getAtributos()[0]->getNome()).'($selecionado);
        if(!isset($_POST[\'deletar_' . $nomeDoObjeto . '\'])){
            $this->view->confirmarDeletar($selecionado);
            return;
        }
        if($this->dao->excluir($selecionado)){
            echo "excluido com sucesso";
        }else{
            echo "Errou";
        }
    	echo \'<META HTTP-EQUIV="REFRESH" CONTENT="0; URL=index.php?pagina=' . $nomeDoObjeto . '">\';    
    }
	public function listarJSON() 
    {
		$' . $nomeDoObjeto . 'Dao = new ' . $nomeDoObjetoMa . 'DAO ();
		$lista = $' . $nomeDoObjeto . 'Dao->retornaLista ();
		$listagem = array ();
		foreach ( $lista as $linha ) {
			$listagem [\'lista\'] [] = array (';
        $i = 0;
        foreach ($objeto->getAtributos() as $atributo) {
            $i ++;
            $nomeDoAtributoMA = strtoupper(substr($atributo->getNome(), 0, 1)) . substr($atributo->getNome(), 1, 100);
            $codigo .= '
					\'' . $atributo->getNome() . '\' => $linha->get' . $nomeDoAtributoMA . ' ()';
            if ($i != count($objeto->getAtributos())) {
                $codigo .= ', ';
            }
        }
        
        $codigo .= '
						
						
			);
		}
		echo json_encode ( $listagem );
	}			

	
		';
        
        $codigo .= '
}
?>';
        
        $geradorDeCodigo->codigo = $codigo;
        $geradorDeCodigo->caminho = 'sistemasphp/' . $nomeDoSite . '/src/classes/controller/' . strtoupper(substr($objeto->getNome(), 0, 1)) . substr($objeto->getNome(), 1, 100) . 'Controller.php';
        
        return $geradorDeCodigo;
    }

    public static function geraCodigoDeObjeto(Objeto $objeto, $nomeDoSite)
    {
        $geradorDeCodigo = new GeradorDeCodigoPHP();
        $nomeDoObjetoMa = strtoupper(substr($objeto->getNome(), 0, 1)) . substr($objeto->getNome(), 1, 100);
        
        $codigo = '<?php
	
/**
 * Classe feita para manipulação do objeto ' . $nomeDoObjetoMa . '
 * feita automaticamente com programa gerador de software inventado por
 * @author Jefferson Uchôa Ponte <j.pontee@gmail.com>
 */
class ' . $nomeDoObjetoMa . ' {';
        if ($objeto->getAtributos()) {
            foreach ($objeto->getAtributos() as $atributo) {
                $nome = $atributo->getNome();
                $nome2 = strtoupper(substr($atributo->getNome(), 0, 1)) . substr($atributo->getNome(), 1, 100);
                
                $codigo .= '
	private $' . $nome . ';';
            }
            $codigo .= '
    public function __construct(){
';
            foreach ($objeto->getAtributos() as $atributo) {
                if(substr(trim($atributo->getTipo()), 0, 6) == 'Array '){
                    $atrb = explode(' ', $atributo->getTipo())[2];
                    $codigo .= '
        $this->'.$atributo->getNome().' = array();';
                    
                }
            }
            $codigo .= '
    }';
            foreach ($objeto->getAtributos() as $atributo) {
                
                $nome = strtolower($atributo->getNome());
                $nome2 = strtoupper(substr($atributo->getNome(), 0, 1)) . substr($atributo->getNome(), 1, 100);
                
                if ($atributo->getTipo() == Atributo::TIPO_INT || $atributo->getTipo() == Atributo::TIPO_FLOAT || $atributo->getTipo() == Atributo::TIPO_STRING) 
                {
                    
                    $codigo .= '
	public function set' . $nome2 . '($' . $nome . ') {';
                    $codigo .= '
		$this->' . $nome . ' = $' . $nome . ';
	}';
                } 
                else {
                    
                    if(substr(trim($atributo->getTipo()), 0, 6) == 'Array '){
                        $atrb = explode(' ', $atributo->getTipo())[2];
                        $codigo .= '

    public function add'.ucfirst($atrb).'('.ucfirst($atrb).' $'.strtolower($atrb).'){
        $this->'.$nome.'[] = $'.strtolower($atrb).';
    
    }';
                        
                        
                        
                    }else{
                        $codigo .= '
	public function set' . $nome2 . '(' . $atributo->getTipo() . ' $' . $nome . ') {';
                        
                        $codigo .= '
		$this->' . $nome . ' = $' . $nome . ';
	}';
                    }
                    
                    
                } // fecha o caso contrario. o atributo sendo objeto
                
                $codigo .= '
	public function get' . $nome2 . '() {
		return $this->' . $nome . ';
	}';
            }
        }
        
        $codigo .= '
}
?>';
        
        $geradorDeCodigo->codigo = $codigo;
        $geradorDeCodigo->caminho = 'sistemasphp/' . $nomeDoSite . '/src/classes/model/' . strtoupper(substr($objeto->getNome(), 0, 1)) . substr($objeto->getNome(), 1, 100) . '.php';
        
        return $geradorDeCodigo;
    }

    
    public function geraBancoPG(Software $software)
    {
        $objetosComRelacionamento = array();
        $this->codigo = '';
        foreach ($software->getObjetos() as $objeto) {
            $this->codigo .= 'CREATE TABLE ' . strtolower($objeto->getNome());
            $this->codigo .= " (\n";
            $i = 0;
            foreach ($objeto->getAtributos() as $atributo) {
                $i ++;
                $flagPulei = false;
                if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                    $this->codigo .=  strtolower($atributo->getNome()) . ' serial NOT NULL';
                } else if($atributo->getTipo() == Atributo::TIPO_STRING){
                    $this->codigo .= strtolower($atributo->getNome()) . ' character varying(150)';
                }else if($atributo->getTipo() == Atributo::TIPO_INT){
                    $this->codigo .= strtolower($atributo->getNome()) . '  integer';
                }else if($atributo->getTipo() == Atributo::TIPO_FLOAT){
                    $this->codigo .= strtolower($atributo->getNome()) . ' character  numeric(8,2)';
                }else if(substr($atributo->getTipo(),0,6) == 'Array '){
                    $objetosComRelacionamento[] = $objeto;
                    $flagPulei = true;
                }else{
                    $this->codigo .= 'id_'.strtolower($atributo->getTipo())._.strtolower($atributo->getNome()) . ' integer NOT NULL';
                }
                if ($i == count($objeto->getAtributos())) {
                    foreach ($objeto->getAtributos() as $atributo) {
                        if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                            if(!$flagPulei){
                                $this->codigo .= ",\n";
                            }
                            $this->codigo .= ' CONSTRAINT pk_'.strtolower($objeto->getNome()).'_'.strtolower($atributo->getNome()).' PRIMARY KEY ('.strtolower($atributo->getNome()).')';
                            break;
                        }
                    }
                    $this->codigo .= "\n";
                    continue;
                }
                if(!$flagPulei){
                    $this->codigo .= ",\n";
                }

            }

            $this->codigo .= ");\n";
        }
        $this->caminho = 'sistemasphp/' . $software->getNome() . '/' . strtolower($software->getNome()) . '_banco_pg.sql';
    }
    public function geraBancoSqlite(Software $software)
    {
        $bdNome = 'sistemasphp/' . $software->getNome() . '/' . strtolower($software->getNome()) . '.db';
        if(file_exists($bdNome)){
            unlink($bdNome);
        }
        $pdo = new PDO('sqlite:' . $bdNome);
        $this->codigo = '';
        foreach ($software->getObjetos() as $objeto) {
            $this->codigo .= 'CREATE TABLE `' . strtolower($objeto->getNome());
            $this->codigo .= "` (\n";
            $i = 0;
            foreach ($objeto->getAtributos() as $atributo) {
                $i ++;
                if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                    $this->codigo .= '`' . strtolower($atributo->getNome()) . '`	INTEGER PRIMARY KEY AUTOINCREMENT';
                } else {
                    $this->codigo .= '`' . strtolower($atributo->getNome()) . '`	TEXT';
                }
                if ($i == count($objeto->getAtributos())) {
                    $this->codigo .= "\n";
                    continue;
                }
                $this->codigo .= ",\n";
            }
            $this->codigo .= ");\n";
        }
        $pdo->exec($this->codigo);
        $this->caminho = 'sistemasphp/' . $software->getNome() . '/' . strtolower($software->getNome()) . '_banco_sqlite.sql';
    }

    public function geraINI(Software $software)
    {
        $this->codigo = '
;configurações do banco de dados. 
;Banco de regras de negócio do sistema. 

sgdb = sqlite
host = localhost
porta = 5432 
bd_nome = ../' . strtolower($software->getNome()) . '.db
usuario = root
senha = 123
';
        
        $this->caminho = "sistemasphp/" . $software->getNome() . '/' . strtolower($software->getNome() . '_bd.ini');
    }

    public function geraIndex(Software $software)
    {
        $this->caminho = "sistemasphp/" . $software->getNome() . '/src/index.php';
        $this->codigo = '';
        if (! count($software->getObjetos())) {
            return;
        }
        $this->codigo = '<?php
            
function __autoload($classe) {
            
	if (file_exists ( \'classes/dao/\' . $classe . \'.php\' )){
		include_once \'classes/dao/\' . $classe . \'.php\';
	}
	else if (file_exists ( \'classes/model/\' . $classe . \'.php\' )){
		include_once \'classes/model/\' . $classe . \'.php\';
	}
	else if (file_exists ( \'classes/controller/\' . $classe . \'.php\' )){
		include_once \'classes/controller/\' . $classe . \'.php\';
	}
	else if (file_exists ( \'classes/util/\' . $classe . \'.php\' )){
		include_once \'classes/util/\' . $classe . \'.php\';
	}
	else if (file_exists ( \'classes/view/\' . $classe . \'.php\' )){
		include_once \'classes/view/\' . $classe . \'.php\';
	}
}
            
?>
    
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>' . $software->getNome() . '</title>
<meta name="viewport"
	content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- Bootstrap CSS -->
<link rel="stylesheet"
	href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>EscritorDeSoftware</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">'.$software->getNome().'</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Alterna navegação">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav">';
        
        
        
        foreach ($software->getObjetos() as $objeto) {
            
            $this->codigo .= '<a class="nav-item nav-link" href="?pagina=' . strtolower($objeto->getNome()) . '">' . $objeto->getNome() . '</a>';
        }
        
        $this->codigo .= '
            
    </div>
  </div>
</nav>
	<main role="main">

      <section class="jumbotron text-center">
        <div class="container">
          <h1 class="jumbotron-heading">'.$software->getNome().'</h1>

        </div>
      </section>
      
        <div class="album py-5 bg-light">
            <div class="container">';
        
        
        $this->codigo .= '
            
            
            
<?php
if(isset($_GET[\'pagina\'])){
					switch ($_GET[\'pagina\']){';
        
        foreach ($software->getObjetos() as $objeto) {
            $this->codigo .= '
						case \'' . strtolower($objeto->getNome()) . '\':
						    ' . ucfirst ($objeto->getNome()). 'Controller::main();
							break;';
        }
        
        $this->codigo .= '
						default:
							' . ucfirst ($software->getObjetos()[0]->getNome()) . 'Controller::main();
							break;
					}
				}else{
					' . ucfirst ($software->getObjetos()[0]->getNome()) . 'Controller::main();
				}
                
?>';
        
        $this->codigo .= '
            
                       
              </div>
                
            </div>
      
     </main>        


    <footer class="text-muted">
      <div class="container">
        <p class="float-right">
          <a href="#">Voltar ao topo</a>
        </p>
        <p>Este é um software desenvolvido automaticamente pelo escritor de Software.</p>
        <p>Novo no Escritor De Software? Problema o seu.</p>
      </div>
    </footer>   
            
';
        
        
        
        $this->codigo .= '
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
</html>';
    }

    public function geraStyle(Software $software)
    {
        $this->caminho = "sistemasphp/" . $software->getNome() . '/src/css/style.css';
        $this->codigo = "/*Arquivo css*/";
    }

    public static function geraForm(Objeto $objeto, Software $software)
    {
        $nomeDoObjeto = strtolower($objeto->getNome());
        $nomeDoObjetoMa = strtoupper(substr($objeto->getNome(), 0, 1)) . substr($objeto->getNome(), 1, 100);
        
        $nomeDoSite = $software->getNome();
        $codigo = '<?php
            
/**
 * Classe de visao para ' . $nomeDoObjetoMa . '
 * @author Jefferson Uchôa Ponte <j.pontee@gmail.com>
 *
 */
class ' . $nomeDoObjetoMa . 'View {
	public function mostraFormInserir() {
		echo \'<div class="container">
    
		<!-- Outer Row -->
		<div class="row justify-content-center">
    
			<div class="col-xl-6 col-lg-12 col-md-9">
    
				<div class="card o-hidden border-0 shadow-lg my-5">
					<div class="card-body p-0">
						<!-- Nested Row within Card Body -->
						<div class="row">
    
							<div class="col-lg-12">
								<div class="p-5">
									<div class="text-center">
										<h1 class="h4 text-gray-900 mb-4"> Adicionar ' . $nomeDoObjetoMa . '</h1>
									</div>
						              <form class="user" method="post">';
        
        $atributos = $objeto->getAtributos();
        
        foreach ($atributos as $atributo) {
            $variavel = $atributo->getNome();
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            if(substr($atributo->getTipo(), 0, 6) == 'Array '){
                continue;
            }
            $codigo .= '
                                        <div class="form-group">
                						  <input type="text" class="form-control form-control-user" id="' . $variavel . '" name="' . $variavel . '" placeholder="' . $variavel . '">
                						</div>';
        }
        
        $codigo .= '
                                        <input type="submit" class="btn btn-primary btn-user btn-block" value="Cadastre-se" name="enviar_' . $nomeDoObjeto . '">
                                        <hr>
                                            
						              </form>
                                            
								</div>
							</div>
						</div>
					</div>
				</div>
                                            
			</div>
                                            
		</div>
                                            
	</div>\';
	}
                                            
    public function exibirLista($lista){
           echo \'
                                            
<div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">'.$nomeDoObjeto.'</h6>
                  <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                      <div class="dropdown-header">Menu:</div>
                      <a class="dropdown-item" href="?pagina='.$nomeDoObjeto.'&cadastrar=1">Adicionar '.$nomeDoObjeto.'</a>
                    </div>
                  </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                          
                          
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%"
				cellspacing="0">
				<thead>
					<tr>';
        $i = 0;
        foreach($objeto->getAtributos() as $atributo){
            $i++;
            if($i >= 4){
                break;
            }
            $codigo .= '
						<th>'.$atributo->getNome().'</th>';
        }
        $codigo .= '<th>Ações</th>';
        $codigo .= '
					</tr>
				</thead>
				<tfoot>
					<tr>';
        $i = 0;
        foreach($objeto->getAtributos() as $atributo){
            $i++;
            if($i >= 4){
                break;
            }
            $codigo .= '
                        <th>'.$atributo->getNome().'</th>';
        }
        $codigo .= '<th>Ações</th>';
        $codigo .= '
					</tr>
				</tfoot>
				<tbody>';
        $codigo .= '\';';
        
        $codigo .= '
            
            foreach($lista as $elemento){
                echo \'<tr>\';';
        $i = 0;
        foreach($objeto->getAtributos() as $atributo){
            $i++;
            if($i >= 4){
                break;
            }
            $codigo .= '
                echo \'<td>\'.$elemento->get'.ucfirst ($atributo->getNome()).'().\'</td>\';';
        }
        $codigo .= 'echo \'<td>
                        <a href="?pagina='.$nomeDoObjeto.'&selecionar=\'.$elemento->get'.ucfirst ($objeto->getAtributos()[0]->getNome()).'().\'" class="btn btn-info">Selecionar</a> 
                        <a href="?pagina='.$nomeDoObjeto.'&editar=\'.$elemento->get'.ucfirst ($objeto->getAtributos()[0]->getNome()).'().\'" class="btn btn-success">Editar</a>
                        <a href="?pagina='.$nomeDoObjeto.'&deletar=\'.$elemento->get'.ucfirst ($objeto->getAtributos()[0]->getNome()).'().\'" class="btn btn-danger">Deletar</a>
                      </td>\';';
        
        $codigo .= '
                echo \'<tr>\';
            }
            
        ';
        
        $codigo .= 'echo \'';
        $codigo .= '
				</tbody>
			</table>
		</div>
            
            
            
                </div>
              </div>
            
            
\';
    }
            
            
        public function mostrarSelecionado('.$nomeDoObjetoMa.' $'.$nomeDoObjeto.'){
        echo \'
            <div class="col-lg-3">
              <!-- Default Card Example -->
              <div class="card mb-4">
                <div class="card-header">
                  '.$nomeDoObjetoMa.' selecionado
                </div>
                <div class="card-body">';
        
        foreach($objeto->getAtributos() as $atributo){
            $codigo .= '
                '.ucfirst($atributo->getNome()).': \'.$'.$nomeDoObjeto.'->get'.ucfirst ($atributo->getNome()).'().\'<br>';
        }
        
        $codigo .= '
            
                </div>
              </div>
            </div>\';
    }

	public function mostraFormEditar('.$nomeDoObjetoMa.' $'.$nomeDoObjeto.') {
		echo \'<div class="container">
    
		<!-- Outer Row -->
		<div class="row justify-content-center">
    
			<div class="col-xl-6 col-lg-12 col-md-9">
    
				<div class="card o-hidden border-0 shadow-lg my-5">
					<div class="card-body p-0">
						<!-- Nested Row within Card Body -->
						<div class="row">
    
							<div class="col-lg-12">
								<div class="p-5">
									<div class="text-center">
										<h1 class="h4 text-gray-900 mb-4"> Adicionar ' . $nomeDoObjetoMa . '</h1>
									</div>
						              <form class="user" method="post">';
        
        $atributos = $objeto->getAtributos();
        foreach ($atributos as $atributo) {
            $variavel = $atributo->getNome();
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            $codigo .= '
                                        <div class="form-group">
                						  <input type="text" class="form-control form-control-user" value="\'.$'.$nomeDoObjeto.'->get'.ucfirst ($atributo->getNome()).'().\'" id="' . $variavel . '" name="' . $variavel . '" placeholder="' . $variavel . '">
                						</div>';
        }
        
        $codigo .= '
                                        <input type="submit" class="btn btn-primary btn-user btn-block" value="Alterar" name="editar_' . $nomeDoObjeto . '">
                                        <hr>
                                            
						              </form>
                                            
								</div>
							</div>
						</div>
					</div>
				</div>
                                            
			</div>
                                            
		</div>
                                            
	</div>\';
	}
    
    public function confirmarDeletar('.$nomeDoObjetoMa.' $'.$nomeDoObjeto.') {
		echo \'<div class="container">
    
		<!-- Outer Row -->
		<div class="row justify-content-center">
    
			<div class="col-xl-6 col-lg-12 col-md-9">
    
				<div class="card o-hidden border-0 shadow-lg my-5">
					<div class="card-body p-0">
						<!-- Nested Row within Card Body -->
						<div class="row">
    
							<div class="col-lg-12">
								<div class="p-5">
									<div class="text-center">
										<h1 class="h4 text-gray-900 mb-4"> Deletar ' . $nomeDoObjetoMa . '</h1>
									</div>
						              <form class="user" method="post">';
        
        $atributos = $objeto->getAtributos();
        foreach ($atributos as $atributo) {
            $variavel = $atributo->getNome();
            if ($atributo->getIndice() == Atributo::INDICE_PRIMARY) {
                continue;
            }
            
        }
        
        $codigo .= '                    Tem Certeza que deseja deletar o \'.$'.$nomeDoObjeto.'->get'.ucfirst ($objeto->getAtributos()[1]->getNome()).'().\'
                                        <input type="submit" class="btn btn-primary btn-user btn-block" value="Deletar" name="deletar_' . $nomeDoObjeto . '">
                                        <hr>
                                            
						              </form>
                                            
								</div>
							</div>
						</div>
					</div>
				</div>
                                            
			</div>
                                            
		</div>
                                            
	</div>\';
	}        
            
}';
        $gerador = new GeradorDeCodigoPHP();
        $gerador->caminho = 'sistemasphp/' . $nomeDoSite . '/src/classes/view/' . $nomeDoObjetoMa . 'View.php';
        $gerador->codigo = $codigo;
        return $gerador;
    }
}

?>