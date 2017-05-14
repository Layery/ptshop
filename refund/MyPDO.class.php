<?php
	header('content-type:text/html;charset=utf-8');
	//封装pdo类
	//定义Mypdo类
	class MyPDO{
		//定义数据库初始化属性
		private $type;
		private $host;
		private $port;
		private $user;
		private $psw;
		private $dbname;
		private $charset;
		//要定义属性保存pdo对象
		private $pdo;
		//定义构造方法初始化数据库的连接认证
		function __construct($arr_info = Array()){
			$this -> type = isset($arr_info['type'])?$arr_info['type']:'mysql';
			$this -> host = isset($arr_info['hostname'])?$arr_info['hostname']:'localhost';
			$this -> port = isset($arr_info['port'])?$arr_info['port']:'3306';
			$this -> user = isset($arr_info['username'])?$arr_info['username']:'root';
			$this -> psw = isset($arr_info['password'])?$arr_info['password']:'root';
			$this -> dbname = isset ($arr_info['database'])?$arr_info['database']:'pt';
			$this -> charset = isset($arr_info['charset'])?$arr_info['charset']:'utf8';
			//实例化pdo
			$this -> mypdoInstance();
			//设置异常模式
			$this -> mypdoException();
		}
		//实例pdo对象方法
		private function mypdoInstance(){
			//用异常处理数据库连接认证
			try{                   //PDO唯一会走异常
                $this -> pdo = new PDO("{$this->type}:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}",$this->user,$this->psw);
			}catch(PDOException $e){
				echo "数据库连接认证失败<br/>";
				echo "错误编号:".$e->getCode(),'<br/>';
				echo "错误信息:".$e->getMessage(),'<br/>';
				echo "错误行号:".$e->getLine(),'<br/>';
				echo "错误文件:".$e->getFile(),'<br/>';
				exit;
			}
		}
		//设置错误处理模式为异常模式方法
		private function mypdoException(){
			$this -> pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}
		/*
		 * 显示错误信息
		 * @param Exception:object $e
		 * @return false ,可以暴力显示错误
		 */
		private function mypdoError($e){
			echo "SQL error:<br/>";
			echo "错误编号:".$e->getCode(),'<br/>';
			echo "错误信息:".$e->getMessage(),'<br/>';
			echo "错误行号:".$e->getLine(),'<br/>';
			echo "错误文件:".$e->getFile(),'<br/>';
			exit;
		}


		//操作数据库(进行增删改操作),执行SQL语句
		//@return 受影响的行数
		public function mypdoExec($sql){
			try{
				//pdo的异常时静默的，不能被捕捉
				return $this -> pdo -> exec($sql);
			}catch(PDOException $e){
				//显示错误信息
				$this -> mypdoError($e);
			}
		}
		//定义返回最后插入行的ID货序列值方法
		public function mypdoGetInsertId(){
			return $this -> pdo -> lastInsertId();
		}

		/*
		 * 操作数据库:查询操作;获得一条记录或获得结果集中所有的记录
		 * @param string $sql SQL语句的参数
		 * @return PDOStatement object 返回的是PDOStatement类的对象
		 */
		 //查询操作:获得一条记录
		 public function mypdoFetch($sql){
			 // 利用PDO执行SQL语句,抛出异常
			 try{  // 执行SQL,返回一个PDOStatement类的对象
				$stmt = $this -> pdo -> query($sql);
				// 解析PDOStatement对象获取一条记录
				return $stmt -> fetch(PDO::FETCH_ASSOC);
			 }catch(PDOException $e){
				 // 异常处理
				 $this -> mypodError($e);
			 }
		 }

		 //查询操作:获取多条记录
		 //@return 二维数组
		 public function mypdoFetchAll($sql){
			//利用PDO执行SQL语句,抛出异常
			try{
				//执行SQL
				$stmt = $this -> pdo -> query($sql);
				//解析PDOStatement对象
				return $stmt -> fetchAll(PDO::FETCH_ASSOC);
			}catch(PDOException $e){
				// 异常处理
				$this -> mypdoError($e);
			}
		 }


		
	}

	/*
	//测试
	$test = new MyPDO();
	//$sql = "update test set usernam = 'Jerry' where id = 2";
	//$test -> mypdoExec($sql);
	$sql = "select * from test";
	$record = $test -> mypdoFetchAll($sql);
	var_dump($record);
	$record = $test -> mypdoFetch($sql);
	var_dump($record);
	*/