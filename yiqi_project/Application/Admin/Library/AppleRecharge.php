<?php
    header('Access-Control-Allow-Origin:*');
    //
    /*
      {
        "receipt-data": "123",
        "UserID": "111"
     }
     * 
	 */
	
	if(!empty($_POST)){
        //$data = stripslashes(html_entity_decode($_POST)); //$info是传递过来的json字符串
		//var_dump($_POST);
		//$a=$GLOBALS['HTTP_RAW_POST_DATA'];
		//write_log($_POST['data']);
		$data=$_POST['data'];
		write_log($_POST);
		//$data=stripslashes($_POST);
		//var_dump($data);
		//echo $data;
		//write_log($data);
		$data = json_decode($data,TRUE);
		$receipt_data=$data['receipt-data'];
		 //$receipt-data=$_POST['receipt-data'];
		 $userid=$data['userid'];
		 $goodid=$data['goodid'];
		 //$errorinfo = json_last_error();
		 //write_log($errorinfo);
    }else{
        $receipt_data="1";
    }
	//write_log($data);
	//var_dump($data);exit;
	/*
	
	*/
	$receipt_data='MIIY/wYJKoZIhvcNAQcCoIIY8DCCGOwCAQExCzAJBgUrDgMCGgUAMIIIoAYJKoZIhvcNAQcBoIIIkQSCCI0xggiJMAoCAQgCAQEEAhYAMAoCARQCAQEEAgwAMAsCAQECAQEEAwIBADALAgEDAgEBBAMMATEwCwIBCwIBAQQDAgEAMAsCAQ4CAQEEAwIBazALAgEPAgEBBAMCAQAwCwIBEAIBAQQDAgEAMAsCARkCAQEEAwIBAzAMAgEKAgEBBAQWAjQrMA0CAQ0CAQEEBQIDAa9AMA0CARMCAQEEBQwDMS4wMA4CAQkCAQEEBgIEUDI1MDAWAgECAgEBBA4MDGNvbS55cS55cXlsYzAYAgEEAgECBBDCDT8ORDdXblPsl9XpDIy8MBsCAQACAQEEEwwRUHJvZHVjdGlvblNhbmRib3gwHAIBBQIBAQQUebXd/EpHMn6HmJJ3N7Ewyr6IsPgwHgIBDAIBAQQWFhQyMDE4LTA2LTI3VDA0OjQ4OjI2WjAeAgESAgEBBBYWFDIwMTMtMDgtMDFUMDc6MDA6MDBaMEQCAQcCAQEEPGWMdzDcWv/ekj lDjB7jdtEzpQ1Kh9hW44XBPjJIJMhoJFy3sED2H9jNVtKGR5o94/Doe3Qw9OE2Ah3kjBTAgEGAgEBBEuYzXf7FHvnfUbDKj74W XFG2Av9ebX0rMnQJRvCTuuOOY5aN5eUmzRjlHW4F9uZ8Iuy5Ebe11GWfgpv8R6c/NkRYDA28zuhi1WkwggFMAgERAgEBBIIBQjGCAT4wCwICBqwCAQEEAhYAMAsCAgatAgEBBAIMADALAgIGsAIBAQQCFgAwCwICBrICAQEEAgwAMAsCAgazAgEBBAIMADALAgIGtAIBAQQCDAAwCwICBrUCAQEEAgwAMAsCAga2AgEBBAIMADAMAgIGpQIBAQQDAgEBMAwCAgarAgEBBAMCAQEwDAICBq4CAQEEAwIBADAMAgIGrwIBAQQDAgEAMAwCAgaxAgEBBAMCAQAwEgICBqYCAQEECQwHeXF5bGNfNjAbAgIGpwIBAQQSDBAxMDAwMDAwNDEwMTE5MzgwMBsCAgapAgEBBBIMEDEwMDAwMDA0MTAxMTkzODAwHwICBqgCAQEEFhYUMjAxOC0wNi0yMlQxMDozNTozOFowHwICBqoCAQEEFhYUMjAxOC0wNi0yMlQxMDozNTozOFowggFMAgERAgEBBIIBQjGCAT4wCwICBqwCAQEEAhYAMAsCAgatAgEBBAIMADALAgIGsAIBAQQCFgAwCwICBrICAQEEAgwAMAsCAgazAgEBBAIMADALAgIGtAIBAQQCDAAwCwICBrUCAQEEAgwAMAsCAga2AgEBBAIMADAMAgIGpQIBAQQDAgEBMAwCAgarAgEBBAMCAQEwDAICBq4CAQEEAwIBADAMAgIGrwIBAQQDAgEAMAwCAgaxAgEBBAMCAQAwEgICBqYCAQEECQwHeXF5bGNfNjAbAgIGpwIBAQQSDBAxMDAwMDAwNDEwMTU0NTMwMBsCAgapAgEBBBIMEDEwMDAwMDA0MTAxNTQ1MzAwHwICBqgCAQEEFhYUMjAxOC0wNi0yMlQxMjoxMDozOVowHwICBqoCAQEEFhYUMjAxOC0wNi0yMlQxMjoxMDozOVowggFMAgERAgEBBIIBQjGCAT4wCwICBqwCAQEEAhYAMAsCAgatAgEBBAIMADALAgIGsAIBAQQCFgAwCwICBrICAQEEAgwAMAsCAgazAgEBBAIMADALAgIGtAIBAQQCDAAwCwICBrUCAQEEAgwAMAsCAga2AgEBBAIMADAMAgIGpQIBAQQDAgEBMAwCAgarAgEBBAMCAQEwDAICBq4CAQEEAwIBADAMAgIGrwIBAQQDAgEAMAwCAgaxAgEBBAMCAQAwEgICBqYCAQEECQwHeXF5bGNfNjAbAgIGpwIBAQQSDBAxMDAwMDAwNDExMzY1NDI3MBsCAgapAgEBBBIMEDEwMDAwMDA0MTEzNjU0MjcwHwICBqgCAQEEFhYUMjAxOC0wNi0yN1QwNDo0ODoyNlowHwICBqoCAQEEFhYUMjAxOC0wNi0yN1QwNDo0ODoyNlowggFNAgERAgEBBIIBQzGCAT8wCwICBqwCAQEEAhYAMAsCAgatAgEBBAIMADALAgIGsAIBAQQCFgAwCwICBrICAQEEAgwAMAsCAgazAgEBBAIMADALAgIGtAIBAQQCDAAwCwICBrUCAQEEAgwAMAsCAga2AgEBBAIMADAMAgIGpQIBAQQDAgEBMAwCAgarAgEBBAMCAQEwDAICBq4CAQEEAwIBADAMAgIGrwIBAQQDAgEAMAwCAgaxAgEBBAMCAQAwEwICBqYCAQEECgwIeXF5bGNfMzAwGwICBqcCAQEEEgwQMTAwMDAwMDQxMDEyMTMzMTAbAgIGqQIBAQQSDBAxMDAwMDAwNDEwMTIxMzMxMB8CAgaoAgEBBBYWFDIwMTgtMDYtMjJUMTA6NDA6NDFaMB8CAgaqAgEBBBYWFDIwMTgtMDYtMjJUMTA6NDA6NDFaMIIBTQIBEQIBAQSCAUMxggE/MAsCAgasAgEBBAIWADALAgIGrQIBAQQCDAAwCwICBrACAQEEAhYAMAsCAgayAgEBBAIMADALAgIGswIBAQQCDAAwCwICBrQCAQEEAgwAMAsCAga1AgEBBAIMADALAgIGtgIBAQQCDAAwDAICBqUCAQEEAwIBATAMAgIGqwIBAQQDAgEBMAwCAgauAgEBBAMCAQAwDAICBq8CAQEEAwIBADAMAgIGsQIBAQQDAgEAMBMCAgamAgEBBAoMCHlxeWxjXzMwMBsCAganAgEBBBIMEDEwMDAwMDA0MTAxNTQ1NDUwGwICBqkCAQEEEgwQMTAwMDAwMDQxMDE1NDU0NTAfAgIGqAIBAQQWFhQyMDE4LTA2LTIyVDEyOjEwOjU2WjAfAgIGqgIBAQQWFhQyMDE4LTA2LTIyVDEyOjEwOjU2WqCCDmUwggV8MIIEZKADAgECAggO61eH554JjTANBgkqhkiG9w0BAQUFADCBljELMAkGA1UEBhMCVVMxEzARBgNVBAoMCkFwcGxlIEluYy4xLDAqBgNVBAsMI0FwcGxlIFdvcmxkd2lkZSBEZXZlbG9wZXIgUmVsYXRpb25zMUQwQgYDVQQDDDtBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9ucyBDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTAeFw0xNTExMTMwMjE1MDlaFw0yMzAyMDcyMTQ4NDdaMIGJMTcwNQYDVQQDDC5NYWMgQXBwIFN0b3JlIGFuZCBpVHVuZXMgU3RvcmUgUmVjZWlwdCBTaWduaW5nMSwwKgYDVQQLDCNBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9uczETMBEGA1UECgwKQXBwbGUgSW5jLjELMAkGA1UEBhMCVVMwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQClz4H9JaKBW9aH7SPaMxyO4iPApcQmyz3GnxKDVWG/6QC15fKOVRtfXyVBidxCxScY5ke4LOibpJ1gjltIhxzz9bRi7GxB24A6lYogQIXjV27fQjhKNg0xbKmg3k8LyvR7E0qEMSlhSqxLj7d0fmBWQNS3CzBLKjUiB91h4VGvojDE2H0oGDEdU8zeQuLKSiX1fpIVK4cCc4Lqku4KXY/Qrk8H9Pm/KwfU8qY9SGsAlCnYO3v6Z/v/Ca/VbXqxzUUkIVonMQ5DMjoEC0KCXtlyxoWlph5AQaCYmObgdEHOwCl3Fc9DfdjvYLdmIHuPsB8/ijtDTiZVge/iA0kjAgMBAAGjggHXMIIB0zA/BggrBgEFBQcBAQQzMDEwLwYIKwYBBQUHMAGGI2h0dHA6Ly9vY3NwLmFwcGxlLmNvbS9vY3NwMDMtd3dkcjA0MB0GA1UdDgQWBBSRpJz8xHa3n6CK9E31jzZd7SsEhTAMBgNVHRMBAf8EAjAAMB8GA1UdIwQYMBaAFIgnFwmpthhgizruvZHWcVSVKO3MIIBHgYDVR0gBIIBFTCCAREwggENBgoqhkiG92NkBQYBMIHMIHDBggrBgEFBQcCAjCBtgyBs1JlbGlhbmNlIG9uIHRoaXMgY2VydGlmaWNhdGUgYnkgYW55IHBhcnR5IGFzc3VtZXMgYWNjZXB0YW5jZSBvZiB0aGUgdGhlbiBhcHBsaWNhYmxlIHN0YW5kYXJkIHRlcm1zIGFuZCBjb25kaXRpb25zIG9mIHVzZSwgY2VydGlmaWNhdGUgcG9saWN5IGFuZCBjZXJ0aWZpY2F0aW9uIHByYWN0aWNlIHN0YXRlbWVudHMuMDYGCCsGAQUFBwIBFipodHRwOi8vd3d3LmFwcGxlLmNvbS9jZXJ0aWZpY2F0ZWF1dGhvcml0eS8wDgYDVR0PAQH/BAQDAgeAMBAGCiqGSIb3Y2QGCwEEAgUAMA0GCSqGSIb3DQEBBQUAA4IBAQANphvTLj3jWysHbkKWbNPojEMwgl/gXNGNvr0PvRr8JZLbjIXDgFnf4LXLgUUrA3btrj/DUufMutF2uOfx/kd7mxZ5W0E16mGYZ2FogledjjA9z/OjtxhumfhlSFyg4Cg6wBA3LbmgBDkfc7nIBf3y3n8aKipuKwH8oCBc2et9J6YzPWY4L5E27FMZ/xuCk/J4gao0pfzp45rUaJahHVl0RYEYuPBX/UIqc9o2ZIAycGMs/iNAGS6WGDAfKPdcppuVsq1h1obphC9UynNxmbzDscehlD86Ntv0hgBgw2kivs3hi1EdotI9CO/KBpnBcbnoB7OUdFMGEvxxOoMIIEIjCCAwqgAwIBAgIIAd68xDltoBAwDQYJKoZIhvcNAQEFBQAwYjELMAkGA1UEBhMCVVMxEzARBgNVBAoTCkFwcGxlIEluYy4xJjAkBgNVBAsTHUFwcGxlIENlcnRpZmljYXRpb24gQXV0aG9yaXR5MRYwFAYDVQQDEw1BcHBsZSBSb290IENBMB4XDTEzMDIwNzIxNDg0N1oXDTIzMDIwNzIxNDg0N1owgZYxCzAJBgNVBAYTAlVTMRMwEQYDVQQKDApBcHBsZSBJbmMuMSwwKgYDVQQLDCNBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9uczFEMEIGA1UEAww7QXBwbGUgV29ybGR3aWRlIERldmVsb3BlciBSZWxhdGlvbnMgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDKOFSmy1aqyCQ5SOmM7uxfuH8mkbw0U3rOfGOAYXdkXqUHI7Y5/lAtFVZYcC1xG7BSoUL/DehBqhV8mvexj/avoVEkkVCBmsqtsqMu2WY2hSFT2Miuy/axiV4AOsAX2XBWfODoWVN2rtCbauZ81RZJ/GXNG8V25nNYB2NqSHgW44j9grFU57Jdhav06DwY3Sk9UacbVgnJ0zTlX5ElgMhrgWDcHld0WNUEi6Ky3klIXh6MSdxmilsKP8Z35wugJZS3dCkTm59c3hTO/AO0iMpuUhXf1qarunFjVg0uat80YpyejDil5wGphZxWy8P3laLxiX27Pmd3vG2PkmWrAgMBAAGjgaYwgaMwHQYDVR0OBBYEFIgnFwmpthhgizruvZHWcVSVKO3MA8GA1UdEwEB/wQFMAMBAf8wHwYDVR0jBBgwFoAUK9BpR5R2Cf70a40uQKb3R01/CF4wLgYDVR0fBCcwJTAjoCGgH4YdaHR0cDovL2NybC5hcHBsZS5jb20vcm9vdC5jcmwwDgYDVR0PAQH/BAQDAgGGMBAGCiqGSIb3Y2QGAgEEAgUAMA0GCSqGSIb3DQEBBQUAA4IBAQBPz9Zviz1smwvj4ThzLoBTWobot9yWkMudkXvHcs1Gfi/ZptOllc34MBvbKuKmFysa/Nw0Uwj6ODDc4dR7Txk4qjdJukw5hyhzsr0ULklS5MruQGFNrCk4QttkdUGwhgAqJTleMa1s8Pab93vcNIx0LSiaHP7qRkkykGRIZbVf1eliHe2iK5IaMSuviSRSqpd1VAKmuu0swruGgsbwpgOYJdWNKIByn/c4grmO7i77LpilfMFY0GCzQ87HUyVpNurcmV6U/kTecmmYHpvPm0KdIBembhLoz2IYrFHjhga6/05Cdqa3zr/04GpZnMBxRpVzscYqCtGwPDBUfMIIEuzCCA6OgAwIBAgIBAjANBgkqhkiG9w0BAQUFADBiMQswCQYDVQQGEwJVUzETMBEGA1UEChMKQXBwbGUgSW5jLjEmMCQGA1UECxMdQXBwbGUgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkxFjAUBgNVBAMTDUFwcGxlIFJvb3QgQ0EwHhcNMDYwNDI1MjE0MDM2WhcNMzUwMjA5MjE0MDM2WjBiMQswCQYDVQQGEwJVUzETMBEGA1UEChMKQXBwbGUgSW5jLjEmMCQGA1UECxMdQXBwbGUgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkxFjAUBgNVBAMTDUFwcGxlIFJvb3QgQ0EwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDkkakJH5HbHkdQ6wXtXnmELes2oldMVeyLGYneUts9QerIjAC6BgFAJ039BqJj50cpmnCRrEdCjuQbKsMflZ56DKRHi1vUFjczy8QPTc4UadHJGXL1XQ7Vf1b8iUDulWPTV0N8WQ1IxVLFVkds5T39pyez1C6wVhQZ48ItCD3y6wsIG9wtj8BMIy3Q88PnT3zK0koGsjzrW5DtleHNbLPbU6rfQPDgCSC7EhFi501TwN22IWq6NxkkdTVcGvL0GzPvjcM3mo0xFfh9Ma1CWQYnEdGILEINBhzOKgbEwWOxaBDKMaLOPHd5lc/9nXmW8Sdh2nzMUZaF3lMktAgMBAAGjggF6MIIBdjAOBgNVHQ8BAf8EBAMCAQYwDwYDVR0TAQH/BAUwAwEB/zAdBgNVHQ4EFgQUK9BpR5R2Cf70a40uQKb3R01/CF4wHwYDVR0jBBgwFoAUK9BpR5R2Cf70a40uQKb3R01/CF4wggERBgNVHSAEggEIMIIBBDCCAQAGCSqGSIb3Y2QFATCB8jAqBggrBgEFBQcCARYeaHR0cHM6Ly93d3cuYXBwbGUuY29tL2FwcGxlY2EvMIHDBggrBgEFBQcCAjCBthqBs1JlbGlhbmNlIG9uIHRoaXMgY2VydGlmaWNhdGUgYnkgYW55IHBhcnR5IGFzc3VtZXMgYWNjZXB0YW5jZSBvZiB0aGUgdGhlbiBhcHBsaWNhYmxlIHN0YW5kYXJkIHRlcm1zIGFuZCBjb25kaXRpb25zIG9mIHVzZSwgY2VydGlmaWNhdGUgcG9saWN5IGFuZCBjZXJ0aWZpY2F0aW9uIHByYWN0aWNlIHN0YXRlbWVudHMuMA0GCSqGSIb3DQEBBQUAA4IBAQBcNplMLXi37Yyb3PN3m/J20ncwT8EfhYOFG5k9RzfyqZtAjizUsZAS2L70c5vu0mQPy3lPNNiiPvl4/2vIBx9OYOLUyDTOMSxv5pPCmv/K/xZpwUJfBdAVhEedNO3iyM7R6PVbyTi69G3cN8PReEnyvFteO3ntRcXqNxIjXKJdXZD9Zr1KIkIxH3oayPc4FgxhtbCSSsvhESPBgOJ4V9T0mZyCKM2r3DYLP3uujL/lTaltkwGMzd/c6ByxW69oPIQ7aunMZT7XZNn/Bh1XZp5m5MkL72NVxnn6hUrcbvZNCJBIqxw8dtk2cXmPIS4AXUKqK1drk/NAJBzewdXUhMYIByzCCAccCAQEwgaMwgZYxCzAJBgNVBAYTAlVTMRMwEQYDVQQKDApBcHBsZSBJbmMuMSwwKgYDVQQLDCNBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9uczFEMEIGA1UEAww7QXBwbGUgV29ybGR3aWRlIERldmVsb3BlciBSZWxhdGlvbnMgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkCCA7rV4fnngmNMAkGBSsOAwIaBQAwDQYJKoZIhvcNAQEBBQAEggEAiQTQip/KdoYBMBfRislRvFKcDiKGACwmZChkWPQR6ZEwLpvHjex6Red5ppnazsgD95qR9GOKVfqBYrHocNTAmtGE7A6y9UcW7Et4R26UOA6OQJbKCX4hbt4sTxyfWwui2FpdeHEaPLpJMGzGiNdg70LlDFNMDjE3pCfVZsseWcETgBBYwr1q28TZE7DiKuWToDVGt9vz2vVC1bspz1m6BOaxPZ5Tw6TBHwYCHdwzSFyx745KYR/fKFjkGpfH3cHDYX7ive8CWRqxLKftJfhxUHyGsI LdHXEgenQnMuETStXLa2ao/yb42QrwA6Oa7HDK4Vq1JEvQBuzAeg==';
	
    $request=acurl($receipt_data);
	write_log($request);
    //var_dump($receipt_data);exit;
    /**
     * 21000 App Store不能读取你提供的JSON对象
     * 21002 receipt-data域的数据有问题
     * 21003 receipt无法通过验证
     * 21004 提供的shared secret不匹配你账号中的shared secret
     * 21005 receipt服务器当前不可用
     * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
     * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
     * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
     *
     * $receipt_data 苹果返回的支付凭证
     * $sandbox  为1时$url为测试地址，为0时为正试地址
     */
    function acurl($receipt_data, $sandbox=0){

        //小票信息
        $POSTFIELDS = array("receipt-data" => $receipt_data);
        $POSTFIELDS = json_encode($POSTFIELDS);

        //正式购买地址 沙盒购买地址
        // 正式验证地址
        $url_buy     = "https://buy.itunes.apple.com/verifyReceipt";
        // 测试验证地址
        $url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
        //$url = $sandbox ? $url_sandbox : $url_buy;
        $url=$url_sandbox;
        //简单的curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result,true);

        // $data['status']==0  成功
        // $data['receipt']['in_app'][0]['transaction_id']  苹果订单号
        //  $data['receipt']['in_app'][0]['product_id'];  商品价格
        return $data;
    }

	var_dump($request);exit;
	exit;
	//发送服务器
	
    header("Content-type:application/json;charset=utf-8");
    $url="http://192.168.0.106:8080/AppleRecharge.php ";
	//$receipt_original=md5($data['receipt-data']);
	//write_log($data['receipt-data']);exit;
	if(empty($request['receipt'])){
		$param=array(
			"status"=>1,
		);
		
	}else{
		$param=array(
			//"status"=>$request['status'],
			//"receipt-data"=>$request['receipt'],
			"status"=>(empty($request['status']))?'1':$request['status'],
			'original_purchase_date_pst' => $request['receipt']['original_purchase_date_pst'],
			  'purchase_date_ms' => $request['receipt']['purchase_date_ms'],
			  'unique_identifier' => $request['receipt']['unique_identifier'],
			  'original_transaction_id' => $request['receipt']['original_transaction_id'],
			  'bvrs' => $request['receipt']['bvrs'],
			  'transaction_id' => $request['receipt']['transaction_id'],
			  'quantity' =>  $request['receipt']['quantity'],
			  'unique_vendor_identifier' => $request['receipt']['unique_vendor_identifier'],
			  'item_id' =>$request['receipt']['item_id'],
			  'version_external_identifier' =>$request['receipt']['version_external_identifier'],
			  'bid' =>$request['receipt']['bid'],
			  'is_in_intro_offer_period' => $request['receipt']['is_in_intro_offer_period'],
			  'product_id' => $request['receipt']['product_id'],
			  'purchase_date' =>$request['receipt']['purchase_date'],
			  'is_trial_period' => $request['receipt']['is_trial_period'],
			  'purchase_date_pst' => $request['receipt']['purchase_date_pst'],
			  'original_purchase_date' => $request['receipt']['original_purchase_date'],
			  'original_purchase_date_ms' => $request['receipt']['original_purchase_date_ms'],
			
			"userid"=>$userid,
			"goodid"=>$goodid,
			"receipt-data"=>$data['receipt-data'],
		);
	}
    
	write_log($request['receipt']);
	write_log($param);
    $data = json_encode($param);
    list($return_code, $return_content) = http_post_data($url, $data);//return_code是http状态码

    function http_post_data($url, $data_string) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json; charset=utf-8",
                "Content-Length: " . strlen($data_string))
        );
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($return_code, $return_content);
    }
	//日志
    function write_log($data){ 
        $years = date('Y-m');
        //设置路径目录信息
        $url = 'log3.txt';  
        $dir_name=dirname($url);
          //目录不存在就创建
          if(!file_exists($dir_name))
          {
            //iconv防止中文名乱码
          // $res = mkdir(iconv("UTF-8", "GBK", $dir_name),0777,true);
          }
          $fp = fopen($url,"a");//打开文件资源通道 不存在则自动创建       
        fwrite($fp,var_export($data,true)."\r\n");//写入文件
        fclose($fp);//关闭资源通道
    }
	
?>
