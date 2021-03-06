<?php
require_once( './config.php');

if( isset( $_POST['submit'] ) ){
	require_once( 'com/gmo_pg/client/input/EntryTranSuicaInput.php');
	require_once( 'com/gmo_pg/client/input/ExecTranSuicaInput.php');
	require_once( 'com/gmo_pg/client/input/EntryExecTranSuicaInput.php');
	require_once( 'com/gmo_pg/client/tran/EntryExecTranSuica.php');
	
	//入力パラメータクラスをインスタンス化します
	
	//取引登録時に必要なパラメータ
	$entryInput = new EntryTranSuicaInput();
	$entryInput->setShopId( PGCARD_SHOP_ID );
	$entryInput->setShopPass( PGCARD_SHOP_PASS );
	$entryInput->setOrderId( $_POST['OrderID'] );
	$entryInput->setAmount( $_POST['Amount']);
	$entryInput->setTax( $_POST['Tax']);
	
	
	//決済実行のパラメータ
	$execInput = new ExecTranSuicaInput();
	$execInput->setOrderId( $_POST['OrderID'] );
	
	//商品・サービス名・メールアドレスは必須です。
	$execInput->setItemName( mb_convert_encoding( $_POST['ItemName'] , 'SJIS' , PGCARD_SAMPLE_ENCODING ) );
	$execInput->setMailAddress( $_POST['MailAddress'] );
	
	
	//実際には、以降のパラメータをお客様が直接入力することは無く、
	//購買内容を元に加盟店様システムで生成した値が設定されるものと思います。
	$execInput->setShopMailAdress( $_POST['ShopMailAdress'] );
	$execInput->setSuicaAddInfo1( mb_convert_encoding( $_POST['SuicaAddInfo1'] , 'SJIS' , PGCARD_SAMPLE_ENCODING ) );
	$execInput->setSuicaAddInfo2( mb_convert_encoding( $_POST['SuicaAddInfo2'] , 'SJIS' , PGCARD_SAMPLE_ENCODING ) );
	$execInput->setSuicaAddInfo3( mb_convert_encoding( $_POST['SuicaAddInfo3'] , 'SJIS' , PGCARD_SAMPLE_ENCODING ) );
	$execInput->setSuicaAddInfo4( mb_convert_encoding( $_POST['SuicaAddInfo4'] , 'SJIS' , PGCARD_SAMPLE_ENCODING ) );
	$execInput->setPaymentTermDay( $_POST['PaymentTermDay'] );
	$execInput->setPaymentTermSec( $_POST['PaymentTermSec'] );
	
	//このサンプルでは、加盟店自由項目１～３を全て利用していますが、これらの項目は任意項目です。
	//利用しない場合、設定する必要はありません。
	//また、加盟店自由項目に２バイトコードを設定する場合、SJISに変換して設定してください。
	$execInput->setClientField1( mb_convert_encoding( $_POST['ClientField1'] , 'SJIS' , PGCARD_SAMPLE_ENCODING ) );
	$execInput->setClientField2( mb_convert_encoding( $_POST['ClientField2'] , 'SJIS' , PGCARD_SAMPLE_ENCODING ) );
	$execInput->setClientField3( mb_convert_encoding( $_POST['ClientField3'] , 'SJIS' , PGCARD_SAMPLE_ENCODING ) );
	
	
		
	
	
	
	//取引登録＋決済実行の入力パラメータクラスをインスタンス化します
	$input = new EntryExecTranSuicaInput();/* @var $input EntryExecTranSuicaInput */
	$input->setEntryTranSuicaInput( $entryInput );
	$input->setExecTranSuicaInput( $execInput );
	
	//API通信クラスをインスタンス化します
	$exe = new EntryExecTranSuica();/* @var $exec EntryExecTranSuica */
	
	//パラメータオブジェクトを引数に、実行メソッドを呼びます。
	//正常に終了した場合、結果オブジェクトが返るはずです。
	$output = $exe->exec( $input );/* @var $output EntryExecTranSuicaOutput */

	//実行後、その結果を確認します。
	
	if( $exe->isExceptionOccured() ){//取引の処理そのものがうまくいかない（通信エラー等）場合、例外が発生します。

		//サンプルでは、例外メッセージを表示して終了します。
		require_once( PGCARD_SAMPLE_BASE . '/display/Exception.php');
		exit();
		
	}else{
		
		//例外が発生していない場合、出力パラメータオブジェクトが戻ります。
		
		if( $output->isErrorOccurred() ){//出力パラメータにエラーコードが含まれていないか、チェックしています。
			
			//サンプルでは、エラーが発生していた場合、エラー画面を表示して終了します。
			require_once( PGCARD_SAMPLE_BASE . '/display/EntryExecError.php');
			exit();
		}

		//例外発生せず、エラーの戻りもなく、3Dセキュアフラグもオフであるので、実行結果を表示します。
	}
	
}

//EntryExecTranSuica入力・結果画面
require_once( PGCARD_SAMPLE_BASE . '/display/EntryExecTranSuica.php' );