<?php 
/**
  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 */

require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Payment/Authorization/AbstractPaymentMethodWrapper.php';


/**
 * 
 * @author Thomas Hunziker
 * @Method()
 */
class Customweb_PayEngine_Method_DefaultMethod extends Customweb_Payment_Authorization_AbstractPaymentMethodWrapper {

	/**
	 * This map contains all supported payment methods.
	 *        		  	 			   		
	 * @var array
	 */
	protected static $paymentMapping = array(
		'creditcard' => array(
			'machine_name' => 'CreditCard',
 			'method_name' => 'Credit Card',
 			'parameters' => array(
				'pm' => 'CreditCard',
 				'brand' => '',
 			),
 			'not_supported_features' => array(
				0 => 'ServerAuthorization',
 				1 => 'Moto',
 				2 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'americanexpress' => array(
			'machine_name' => 'AmericanExpress',
 			'method_name' => 'American Express',
 			'parameters' => array(
				'pm' => 'CreditCard',
 				'brand' => 'American Express',
 			),
 			'not_supported_features' => array(
				0 => 'ServerAuthorization',
 				1 => 'ExternalCheckout',
 			),
 			'credit_card_information' => array(
				'issuer_identification_number_prefixes' => array(
					0 => '34',
 					1 => '37',
 				),
 				'lengths' => array(
					0 => '14',
 					1 => '15',
 				),
 				'validators' => array(
					0 => 'LuhnAlgorithm',
 				),
 				'name' => 'American Express',
 				'cvv_length' => '4',
 				'cvv_required' => 'true',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAPqElEQVR42t2aiXsU9RnH+xd4gILKjVwiIB6tWFuxtWhFaStt0aotttqqRUBFOSImQSAJCWcIN3QAw00Ew5GEHBPItbnvhGw2x26ySXaz2St3Anz7vr+Z3Z0cINqnPq37PN9nJ7M7O+/n956/gR/96If0ej/FvmJpmkNaluGSVuhc0krSZ1kuKSDHLRSoyvM3f+anU767jMTX/jPVIb2d2iItTG6RXpNt0vxEmzT3klV6LtYiPX2hUXos2ixNPWuWxkbVS8OOm6Q7jhmlOyJrpLsOV0lDDhqkoZJeGrq/Qhq2t1watrtUum9XifRARIk0IrxAGrU1Xxq9JVcauzFHGh9KCtFJDwbrpInr0qRJgelTvCCL0x3yMp0TKzJdICPhn+3Gmhw31uW2Yj0pSBUf87kv6LMA+s5q+u5KuoavXZruwPupdrx9xY4/J7dgQZINv01oxgtxFsy+2ISfnG/EjK/NmPhVPUacMuHuY0bccaQGd35ZjbsPGTBEqsQ9Bypw776rGL6nDPftKsX9O4oxYnshRm4rwOgteRizKRfjwrIxPiQTE4J0mLguHVPWpjzrBVmW4ZQZgg0LJAPXqoZvyGtFaH4bwlTxMZ8LVoHIS31gFqc58G6KHX+93ILXZRt+n9iMly5ZQV7BTy804tHoBkw5U48xp+twzwkGqcWdkdUgr2DIwUoM/Zce9+6/imF7yjUgRQJk1JZ8ATKWQTZkgTyCiesz+oJ8qnPKn6kQbGAIGcuGbypow2bSFlV8zOfCVKD1GpgVBPJRhhOLCOadFK1XrHievPJz8soT5xrw8FkzxkfVYfhJE+48yiA1AuTugwYBcg+D7C3H8N0EsrMED0QQSDiBbPWA5NwcZFWWW/ZXPcEQG/MVo7cWtiG8sB3bixTx8bZCBWqjCsPgHGZ+5BVaECxJV7zyFnnlT+SVV8grL5JXno1pwpNqeE2g8HqAQO5Sw+suNbwUkAoVhMJLBRkRXihARm/OA+UJxgkQCq/+IKsJhHOCwylMhdhGRkeQ8TuL27FLFR/zOf5si+qZYDVntF6hxMffrrTgjWQb/qCG1y8J5CkCmUkgkwhkJOfJ8ZvlSbmSJwKkeCBIKIGEeEAyfCCfZ7vltWQMr/Am1RMeiN0lHdij0S4vjBJmoapX2KOryCsfE8gHmvD6Y1Iz5sVb8SvKk6fVPJlMeTKK8mTIcTVPbprwBLJjMJBsBSQoAxO0IBQaMsd7qOoNDiEPxF7SPlV7VRj+LFzjFfZkoBpen6hJ/3cC+YuaJ78hkDkE8jMCeYxAHiKQ0Zzwx30Jf/ehqkErlwdk5NYCATJGBRk/GAgZIXvCio3brnpjjwqwv1TRPo1X+DueXAlSk94vSwFZQiD/YBDKk1fVMswJ/zNK+MdVkLGeynVUU7kIZOhgINv/V0DivyeQH0xoiWRXG+D/dbJ/L+WXQP7r5fcH0xD/qyNK/Pc4ogw2NAb/Pw6NPMZvKHQjvr4LiaQkcxeOGzpwuaEbV1TFmbqEB04aOpFMn8tm5bt8TVxdJ2JMnThv7MDXtR34qqYDJ6vbcayqHV8aKERL3Vika8HOq26El7mwqdSJFxOasDTThuBCO0IKWhCS30KLZENobjMWy2Z8ntqIjVkWbMpsQlBqA949V40t6Q3YmmbG1pT6wcd42kfIOms3zG3XkE3vVa5edF67gdaeG8hv7hG6fgPYV9aO9t4bKLP3IpfOmduvCWXSNRmWbqQ1dSOruRuO7utIauhEvLkTsfWdSLN0iXN87anaNlg6r4Fftq5riKppJfhWOOnzY3oXjlU40Us3a+u5jvMGF06U2dHQ2gMXfbfU0o6LV+3i2kFBlmc50nro4jDyin+OC2tyXfRjEEBcUrnRseF8M3vXdTrnxHJSLHniInniY50DYUVuWl1acVr9EnuPKLlLdHah52KbsDClGfUEzdUq1dIJ/3w7hlKiPxVdh7eTGxFRbMfkyEqRH5dq3cLYJw9dFfnx5hmD+HsLeePnuwsVEAqrSWsJJFADElffVVPl7hXxrXf2gnJGeMZAINzgpIp2bCxsFT9w3tiJzWR0IMFeIIhzFE6BuU6UO3pQSqpp7UUxgfDOsLHjGsix2FzqwlMXGlBLnw0/YUKMuQNDKNHDihziN9kDj56oFmH1RowJr503ivOzCOSNs1VidxhX6RAgz6ggEwVIWl+QanevI4JWclW2E1cJJKTATXIJKG5ufI63sjkUNiuynCKU/Oi7nA9nSH+l7e3iDDveT7MjtMiFwpZu/ILKLSc4jyRcqSLKXTC19Yot7llju0jyBHO7MOqCsVVUqzMUSsFZVlF2K+1dmHWwHJszGvHpJSOe3luEDZfr8MyuAgWEwmryF/1A8mw9lteoyixKt+MjCpNlmUpD47DhUcPWeR07ylqxNMMhqhPnyyf0HU7q0zXtCMxzQfvKJxCG4HcjGe+Xa8eMaLMA4ZKb0tSJX8eaqcpZ6bevYX5MHSYc0uPxo5X4caQeY3eXwC/ZjFlSmQBp6ejFjO35eGRrLmbv1IKk9gXxz3XmLbxsE2HgeVHeYAkZzv2gmUAqyCt8HE2hxS/2AFemEyTezs6lMjuHwum15Gbk2bpFz+BCwImeQIk/jEKKobgBMsjXlPTjjlZ5e0eiqc17763ZVkzaVYwZe0sIpEGciyywirI7e2e++JvDavKafiCZ1i6Lk6pECcU4D3jpdHM3/W2l1bISBAO2UcXhY35n2bp8x02UC42quCJ1k8sYgq/jKtVFBwzRe0N557+r3D3i3UQVyUTHnCfsjbcuGsX5Ole3EFer7PpWtFOhqXN2odHdLUDUsOoL8p7Ols3V5XVaTV7VP9H7ymwH/HJ8Yg/w+0eZdhF6K+jzT0gfkeeW0rXcJ95Lt+GdNBveSrXhzSvNWJBsxdMxjXg50YK51Ddeim/Ay3ENmBdnxrgjVRhxUI85Z2rxu3NGzD1TLXJjNOkPUVX4Y5QBC07p8epJPZ47UIyXpRK8fqQMb0SW4s3DJcIbDwVcwbTVsg+EOq78YwqFpyiuec8wmxKVk5U7Mk+tc1TxMZ/j/TfPTjx2cC7wMysez6fTHMWd+0EaQUaeqhPhpMxTmlFEHQ77jCPqpNunk/ebdrXPsiap3njIvx8I3VxmI2aSMY8TEBs2i8RG8ujN+4gD+lbc7MWN7rdJFsTUdwz4jBtrhrVThJL2xaGUY+lAgbVz0N/kkEqucaGLG5rmdcXggL3d91uR6fUfekFotyZP+KpOrCaXSoZ6hMSz0WOqdtN4UUT94aUEC16Mb8ILpOcvkdfiGnG4qk3kA+fUM7FUcs/xb9Rj2tk6TD9jormrSYB8ntWMR0/W4NHj1XjsuAGvXjCi0tGFvYU2an4V1DfKRaWaf0IvOvuaJBOe3VcoSu7sHUqShyebMG9nLn61JQtzNukwJzTteS/IvSdNMo/VoykcxlFYTCDxo03eAD2kivtAZnOXOHe+rgM6Os6wdiGczo8+bcJBQyuevNiAFxIa8VmeHZOiTKI6naVkn0zGL0m1YNEVCknKidSGdlGlnjiix5LEejFTPX9Mj/S6VqG8hjZEl7dgAoUVjyQ6owu6WhfKmtpEbkRmNiCrxoGsage2Xap+xwtCtV3mWB5KEynH9f0Exc9nRxEYb0lZ3J3ZcD43L7EJv5cteIXEr7WFDtEfniBPtFEo8CD44IlaHDG4sYsGxFGRVcIbH1xuFJ4IzWlGYHoTxuwpxekKhxgMp+4pxoLTlVhAyb3ofDXs1Du4b/yOkvy9U1dxNLcJ1ygc50bk4DcRufj7oSJIKSbsSjC86wOJrJG5vouk5D0CQXEH5q3oUBLvrTcUO8WMxOe2kxeOVLfhSFWryIEXLjVi+LFa8oIRelcPggvsuI+Mnxdbj1di6uGns6KFwu6D5AZM+1KPxUlmvBVjFI0vpoqmYR15isLqZGkLTpXYcLFCGQwNtk5EFVqx7lKN6Bt6GhqbXF2ILrBgt1yLR1bLmLE8YaEX5K7D1TJvN3ls4OqiQClgHgXRXMShwscrclrE38HkifmJjZh0mgCcPSIXHomqxcfpVsw8VSuM4T6ha+yApb1XjOevRNeKPsE5sIBK7ofxJmyi7v3TAyViTOd56kBOk5h0t6XUYduVOnwYVYGp69JQ1dyBiKRa7EiswZqzFZi+MgGzVifN94LQ7kzmssjl0SsG84gAOVwqaLXfo7Hi/X5iiGyqPhxWn2ZYRS74Z1oFCO+/ucSm0VwVWebA9rxmaoDdSK9vw9GSFhwtbkGswYmPY2qxLKYGn1ysRhAleXadG8ujDaRKrI+tRmaNU1SrVVFX4Xe6HCHn9Cipc+FoutFXtaimy1zbeZc2hLacvO1kMJ+q8M9UK1JoZbVKVXW4woXhdO1yguBE9iiNNIx6BG9buTKlmdsEwFm9A/vyrSKxM1gmt1c6o1tJbjXBM2uduFDSjNC4apHcHiWXN+PLFCMWSwU+j9yzXy/z0wtuUl5JetG4BhN/pv0uX8sPDXib6vGAAqA8m+KOzc3O8yDB2/DUf7zxNj3PQ4WgDKXxeeapgBRM9b+Mh1cnY5qfTCGViOkrEjCDNHNlvK8h3ru3XOZnSSzutmyUMO5W4u/sV65hCePVLs0e6AugdOw+EJtvE0Lt4ALiMxnTNBADQGjlZDaAV9FjEItX12OoT+W+VRcr7zF+cIAH+gHwQwQvRNjtQUz9PBkPE8R0v6Q+EANA6MYyzzpsBBujqEwxcDDt7me4x3htCHkAaKvKjzv7e4GfhvCDNp6h+InIzSEuC4hpDEFV6pYg90cUyfxoko24XzWIDVPgBpHHaNVwj/EKQKHXA14AyoXBQskLsf67QQwAGRFeJAsDthd5DVJULJ699lFEcZ/viJX3GM+rrw2hfgBeL3hDSQPxxbeHGABCN5bFCnoUrhh2K430rPo2jfH9Vr8vgJoL2lBar3qBx3K1Omlz4psgBoJszpOFASReSY9R3yjPqnuM36wYz4/+RQhpAHy5oDwh9IbSF6oXaJPkLbG3CTEAhG4qj9mY611FNogNu5W8RmtXXrP6/KC5rwf65YI2lALUUFIhpt8mxAAQurHMN2cjuCR6jfomhWkM1xivrP7gAJP6AXjzoV+zux2IASAPhmTJfPPxqiFsECel18AByvIZ7TF8MONvAuDLBQXg4W8RSrcEocST+eZshCLVqJCbKNhntNdw/tejPsYPDCEvAOWCSGiPF1Z9N4gBIHRjmW/OVYQNmeBR0E20XmM0X7Mu3bfyWuP7h5AWoJ8Xpi+P/9YQA0DohgvJgICBygggg/uIzw3+XdKatIApQikBU/x9mrr6slfkAUUrEwOmLVc04z/QzJXy2B/U/zn7N6D+no2/EO8pAAAAAElFTkSuQmCC',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'dankort' => array(
			'machine_name' => 'Dankort',
 			'method_name' => 'Dankort',
 			'parameters' => array(
				'pm' => 'CreditCard',
 				'brand' => 'Dankort',
 			),
 			'not_supported_features' => array(
				0 => 'AliasManager',
 				1 => 'HiddenAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'Moto',
 				4 => 'Recurring',
 				5 => 'ExternalCheckout',
 			),
 			'credit_card_information' => array(
				'issuer_identification_number_prefixes' => array(
					0 => '5019',
 					1 => '4571',
 				),
 				'lengths' => array(
					0 => '16',
 				),
 				'validators' => array(
					0 => 'LuhnAlgorithm',
 				),
 				'name' => 'Dankort',
 				'cvv_length' => '3',
 				'cvv_required' => 'true',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFgAAAAyCAYAAADY+hwIAAAEN0lEQVR42u2cr1MbQRTHIyoqKhB0psNdcjt3exlmQFRUVFTEIRAVFQhEBQKJQERURCAqEBGIioiKCmQFAlGBqIiI6B9QgUAwA/kx04gKBH1v7xKS3F12b3dzzV32zbxhIGS5++Td2+97u0upZCwbq5VqzzxCDt0KaXiO8wm/Ghf3ETMCFoFb3aiuwy+04LVNE2ryBvyeA8djCNS9qRdohTThxTWDSI9hNHu2R0fU1zC0DRZ9hhkBmNbZN27FfUcJ2TFYtEfxSZAeCKmhi7ypW6bve5Z/3rPpDfhjgf0h5mc3fZtegdcHAul0nBVEAA/IJoE/0Ck4VHG3vOueRd9qAXxfdt/AoLcGbDTK+5a/qwQYHwX2aRmYST68G6kFGcA92z81EOd716bfpAA/QmUHA9wbiFz/GzfpcQFjEjfwxLwPUjc14K7lfVzAxdwGM3DoNh2kyXdT7xV1nqQUH6cdyrSI39u0piTTFmnXUL8HSsU/gpu4TNCgCOKrzPj9sredCLfsHateP8LF+mBpAc8azsoIMwJaEnCvTHd0fmAzIuADXic+7bkBPFU1Ylp4AnIiNU58qmvjk6MGlx6MgiCXgNnjDZPHBOSGJIjG7DxwR8grxcg9mpJqeQXMIFv+oUq+hBTRnJRUvBKXP563F9HCeQYcRmE77iaE3hvk80QQMjm3cIBZPobIkYy4H2HF9VnxSdpNUjiZAsbJA7tvkw6Ppa0yJlaVKOWko9+mFziGprng/wIOP+m4CxmAKD9TuVGp6Ae4dy+3XkinhaCiHc7tR2QJmFcBqj6qae3PRnVdWpOD2hBp1WYbwTat8/qoWUUxNmFUopc1vMIcvjwRDBHKieCLrKI3XIlpKVWWAlGcKeBJWRTjHVWRn7ZPwLpdc1YeUozzsByAscpByJZ/HnabvuPkFtcQSXODKoCxr62Si3mprwA62PsiCWV/3LOFD1qHIikcYKye8EnQoWhUKzmcNOFafhcGMBYteENJ615pmzKoZ3HiU0oVQX95WAjAmLuVesHRThqmiitVmRj2JPINeCb6dLUqta1kTI6dK8BhWjjTAWSmVTm9Emz5r9UlKdtGlh/AKOVEJxENmvyXaqpglZ7l/1xawKhNcc0srP4SV39ldTAHMPql7NjjSg9Kcalle7ZYKLNMLrN0nna5X/zvDjPZGwGs0gM2G0+EPa5XzQWMk01mEZDzDYBxK9SCm/9oywDkpodmHDshwGwSSre9adX8NqmJJKwieOtRq5wa5mnpVDINJ7w4fbrC3u5uVOeeKUytg4P9wqx87bANHAuICG2ycBEe3HdLtK+dq35wHm0M2AUNFzn6aUzZaIUEq+fsELjjnBok+swre9vUcQ6efuA4x67kzhlj04YBi2e/t2a3C7ADzHhSPOFYkrH5hme+8Ugywk1kiP/rwHWcffP/H6T8yI3pqv0DUKie1NAOCkIAAAAASUVORK5CYII=',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFgAAAAyEAAAAACtAZ+XAAADrUlEQVR42tWaT0jbUBzHvbqT7rBDEaowinjUUBGFjh289OzFggiiCGJ7UGSMZoFC90dcLVgcRRFLdTg3ZCCMXgY1ECi1xa4FWeiQSdlUuolSCq5YYp8xS5PmvWSZqcnvB1J4j5ePv/7e9/1+L21gDGYN4M/l6Pqp3xow+a36dECWO/oLfDr4pPHbob7jevFnybz96wbYM3NeMEIyBEzfeyvA5wW/1RjZezr45lEFeLdvxzAb7vWDCnCsJdZSO5TEAq2TcQdZTx8a4D5Nxr3YNl6kxVQgFySA8yncVl9UaXdFswkFwAfTE916wGVjvheWAS7SrqhecIGPuI83kcBvX+oJF/jiFwRw2T4e1hvw8Am/+WqAswm94QKn16HApEvpIhPdrijwsSbpzGNHpVwsllJziIwX431/VUbW/s1K1MF0xDfbxquogwxa4PNzAuBPD+XW319NYrcKzNnxZtDCQaOA005l/xhr8YWhAdKlCTB7Po64Acj7x/A5fNoRmRKFXi/aC0KgITDD0OsAeasTPmOrk9sFZyH0WhEfO1NTYIb5vIjOzLUeVqzEx67YYmbum9AYmGGIDP+IWgtahBDw3K0bcBKLmeGjL44c5EYOvcJeuFpzVAKXqHyK9d8/0DPL9oNpVPzniLJdfh/8N/Be1aE91hTC0Q+F2xxx0YwazyaEuKqBxaeg3NcKs0I7avQsVFvcqgTexsW1qpoYF2l0fMt2kOO3AryREy4zR6iJbz613IGeURtjlcCsHHGO2+RkH1YZOEhhFyE1p1ojVANHfEFLoNWLzeMhnC9HpB+IBh4Po/NYnH6a6/DKV/gY9QwgzOPyWlI34PiCF5PXGrmTrkhPddUFuERNdVV3YbCCZsSdT6FXysU5PdYUOISjq1yuVnOQXkxOFuMLmgOz8VNSXCrrNtjZGgGXqBAuD8IWl1w/fGiSvbFs1Qg4iUltEjk1f/pBLi3Kds+9WwUutKedGzlhJ4zSYSGwg5xtQ82+vspuRrT5aSe8PYc36rArACkXV2JKPO002EUKX13XAJcoNRHQ+jqQ760lcni5Q2/Aaz3I28tCu/Tl0135RHd1oSSpEuKO6m7TQajUEFnLJnhNvUsnMj8bFb3jAHId8eG24RPlkVAih8odty13SNXcGtXD2tk1cNrCvhI1gr1yV4AvR5/fNwYu3f+u//pd85I5bdE/7uWoZ6Y4dPPzg4BpyQxePOvVzgs7jGeGJWy4aUiOPjbr9dcSfuuKfbePg78CoieM4ZUAWy0AAAAASUVORK5CYII=',
 		),
 		'diners' => array(
			'machine_name' => 'Diners',
 			'method_name' => 'Diners Club',
 			'parameters' => array(
				'pm' => 'CreditCard',
 				'brand' => 'Diners Club',
 			),
 			'not_supported_features' => array(
				0 => 'ServerAuthorization',
 				1 => 'ExternalCheckout',
 			),
 			'credit_card_information' => array(
				'issuer_identification_number_prefixes' => array(
					0 => '300',
 					1 => '301',
 					2 => '302',
 					3 => '303',
 					4 => '304',
 					5 => '305',
 					6 => '309',
 					7 => '36',
 					8 => '38',
 					9 => '39',
 				),
 				'lengths' => array(
					0 => '16',
 					1 => '14',
 				),
 				'validators' => array(
					0 => 'LuhnAlgorithm',
 				),
 				'name' => 'Diners Club',
 				'cvv_length' => '3',
 				'cvv_required' => 'true',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'jcb' => array(
			'machine_name' => 'Jcb',
 			'method_name' => 'JCB',
 			'parameters' => array(
				'pm' => 'CreditCard',
 				'brand' => 'JCB',
 			),
 			'not_supported_features' => array(
				0 => 'AliasManager',
 				1 => 'ServerAuthorization',
 				2 => 'ExternalCheckout',
 			),
 			'credit_card_information' => array(
				'issuer_identification_number_prefixes' => array(
					0 => '3528',
 					1 => '3529',
 					2 => '353',
 					3 => '354',
 					4 => '355',
 					5 => '356',
 					6 => '357',
 					7 => '358',
 				),
 				'lengths' => array(
					0 => '16',
 				),
 				'validators' => array(
					0 => 'LuhnAlgorithm',
 				),
 				'name' => 'JCB',
 				'cvv_length' => '3',
 				'cvv_required' => 'true',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEEAAAAyEAAAAAB2ujW1AAAEQUlEQVR42mP4P+CAYdQJ2Jxwt2Dl23k98wUWVC6csmj94hdLWZblLO9YsXjlvlcHkdXd0Vv7ZVHuoj2LDi3WX1y/RHpJ9pIbSyYu1V1a+1IEWd3NnTu+bDqwxW7ri+2pO/l3Oewx3/tvf9xBtUNhP5yxOOGmffpv85VWXjaNdooOS53uuMa7b/EM8b7hxx/gGnTyKhdM3TWN1PVOx50tXdRcHrlauH5xe+Ae7P7J448nixefV/KlUpi6y40lJ+NMEj+nvMoQyrbKSyzsLEmuUKr2rCtsnNEy4cNlDCfsmWAra1pE2AkH77vscFIn7IStfeFPo3lJcML9dquZxtaEnXB1t/MZJybCTjjzJ5SLRCekcxlsJ8YJKQaOYoSd8Nc9K4VEJzydry9OjBNu3HTgIcYJZyQCH5PohB1OxDlhvQdxTlghQ7ITFjwlzgkLWIhzwqQQkp0w7dmoE0adMOoEypwQ9Tw3rril+FrJ3YRP/rdo4oRG6xddLz695H+57OXhw3cQTogXWyJ4rfTnH9RK/l3Fme2rJkb9gTjhU/Iri1ctry6+zni99Y3+m+o3J97Gv13zlOdc5pzlJDihSx5hweWLsNJxXxG+5kf5XOTKGjvY+48iJ6D4u2h+TvqvmGkxL9I+td3d+undUXQnvP/WEtn2vPNg99VNMn8nw0T/afW4UMUJNyuCYlGTo/efDum0DchOeHURkRaOuCP0zuelghP+esZqE241ITthvxKVQ+EcBzENN0RELC7+yQoTPbiSKmlhVSMxTsAEdxWWy1KYI2BguQl5Tvj//4nUsi9UccLxF8Q4AZEWchYc7EPo3l5EpBOWHEdoOq+I6oSff0IWkpYc808g6T7dnIvhhKPqnuWoTvC89PYdQtPSAvRMeTrU4wO6Ewp1E9bjckLtLWTdE+Ow9iNOlrZdiZhhddvKy35z4Yc7WgiZT/JxHJhF032/Dp6AHJAT/LfkNy3MvmuLu2iaFfbwGkLnFyYcXRlc4H14+R1YNbVb6epuSgvof1rLxbA44dyeQ1/f5GAqf/R9kVzYedSaslx4T8RHNUy1j+0O1qNGBCb4suzy7RlX8OQIB6boulyPlvttEu0mTfPyy0PscFfW4SVF2h083frdjd0XKwzSJXw/j7aaRp0w6oRB6oQbmjRzwl0Z4pzwRoQ4J/ySINEJEbG/cohxQvRskHmEnTDDC23Qj7ATpk0Hj0MQdMKck8Q54ZQwiU6I7nqzkhgnxP75Uk2ME6YG/d5HkhPC7K49g47G4HVCiP2NAog6/E7oynvjijH6is8JBTH3EmHK8Tmh7PYzQZg6fE5YcOCTDpYB4P18tRfqORtUG780C0Bqyk7mLvnudwtnXjVBVn7ErYO9k69zVefZrqyu/K5SSE3Zs2uJ9PV2ZHW7D01unVoz3Xlm+6zncyfNj1oYuHjTUqHlnHt+vogZHYkfpE4AAH4T2J6tZ6Y7AAAAAElFTkSuQmCC',
 		),
 		'mastercard' => array(
			'machine_name' => 'MasterCard',
 			'method_name' => 'MasterCard',
 			'parameters' => array(
				'pm' => 'CreditCard',
 				'brand' => 'MasterCard',
 			),
 			'not_supported_features' => array(
				0 => 'ServerAuthorization',
 				1 => 'ExternalCheckout',
 			),
 			'credit_card_information' => array(
				'issuer_identification_number_prefixes' => array(
					0 => '2221',
 					1 => '2222',
 					2 => '2223',
 					3 => '2224',
 					4 => '2225',
 					5 => '2226',
 					6 => '2227',
 					7 => '2228',
 					8 => '2229',
 					9 => '223',
 					10 => '224',
 					11 => '225',
 					12 => '226',
 					13 => '227',
 					14 => '228',
 					15 => '229',
 					16 => '23',
 					17 => '24',
 					18 => '25',
 					19 => '26',
 					20 => '270',
 					21 => '271',
 					22 => '2720',
 					23 => '51',
 					24 => '52',
 					25 => '53',
 					26 => '54',
 					27 => '55',
 				),
 				'lengths' => array(
					0 => '16',
 				),
 				'validators' => array(
					0 => 'LuhnAlgorithm',
 				),
 				'name' => 'MasterCard',
 				'cvv_length' => '3',
 				'cvv_required' => 'true',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD0AAAAyCAYAAADvNNM8AAAEu0lEQVR42u2ZP2gbVxjAn2rHURObKo1llHsX0ODBgymCmJLBg8BDhiSEDm0pLog20AymZDDBBA8umKDBUBdM4ySOdEOHDBlC0ODBg4cMGjx4yNDBgwdDTW3Hok2qO0WWXr5P/hTOsmyf7j5JIb0PPqTTO717v/f9ue+9J4Qvvvjiiy//U3klpL4t9NFtoU1tCzm/I6QBn7NbQk7+LS5e2RLhbif9KCMaNA09njf0CSutz5iGNEDnTUObMtMyYT3W+9sKmhPR0I7QJwBuFVSdoCZoBiZjpF5feUNetlL6MwAzQdUJ+hInIWdEQy2DVUJ07ghtDKy66QD2kIL1s6Ax7Ms0IlGAWHIAekittMzlU3IcvaPp1oWBL7mBPaABWbK+6nlupmTBDXCNZt8Y0UhTgMG6AzDgNc/AoPkvu5VKCLV354wCcMUAvlEwtIFmWJgF+L9YTwW4qnt3z3JAg2qbbBbHGGZxadB/9PMHgKv6NnmOCVxmWWIcBnuLA3in64IqfdNRF1r9GFDWgwgPOGR2T8D4fnWbpQ/F8aWe+sBVN584wwKNWf3fh1qvh+RVeQ97t3KnpsrffXIstPqBz9pY2HhwbW2FA/p1/7njgUmL059xxfa6h9LSOzCqFf/UEXTp9mkuaPXG2C+AGrQy1tI80KWvOxxBY0LjgsZqzUUSk5NMWbvsCJjUut/GuN4S+hwH9O7ZSKkR6MJsmMvaf7jI3JXlYeuhf+3lgU5pi26gp9sBbc31MRUp0nCzwBhjSWQBqcrfB5xDP7rQvsoMF/xc2bt4/ZQj4PJPHWzZ20zpow1Dr4toEAb8uhUlKHcpClp0XYrCgJ9wQOfCYWdJbOY8F/SyhzJUj7O5+LWuk107xVSYpLVvPS4ttcVmrqXfr6nvhbhWWaue19O74uIgDLrIAV4YCdavuX/uYrMyvJ+HWXZPtoR2kwP61ek6Gwm4gcBUeoJOsu6TQXzPsBQroT5VHg28B25r2ekwvqdYsvnnfeW90VNvC7+FyyxxbMhZ9YvobNreN1RqN2DgOY/gy7sj+hf5tMx43RqCGL7ZwiOdSm1uNgi7Bv9L2PuyHsurmHEbLT7Quk3b5D9O/hJaL0JAvD87qnqD9nUIiwX4fhW3k4/qCw/uzLS2AEBrR4CauHJCy+YXpP7BnF7i7imsw/uxqMFXHXqEq73238PdeGKBE4GfLT2s88WbYOwNt+A5mC+ucHSEroWHY1HQ6pHJoDgYnzrdd5musW2IfkO5DYo7GXFbHwO271GamCHb/2O2ZwRtY4jUuR/b8cB+isA9S7zyihZiBXQT9AXon6AvqX0CdIOuTfoNVzlPCDRCn2uguJuBSz4sJJbof4M0WPw+TXD4nKegMwSGz8/S74ma+2PU/sLWzgK9QrOOIEnQ7sqSe3/GN2igA5W32b6MEdSwrQ/D5upFAsfFwSRBJKk9SddVmaNrfH7GBp08pp0Fujpgu/ssEzRaYJxcWNlcd5CsHyNdIXcPktVv0KToNf3eoskIUf/j9IyhGksnbKGzWtPuWWLkwrWJYtYWVxmamAzN+BxNyoItbg0avE7Ai2Tt/pp+g+S2WZqAELk5es483We/v9q+bGv3xRdffPHlo5N3CwbjLLTGqs0AAAAASUVORK5CYII=',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD0AAAAyEAAAAACaz1CjAAAEV0lEQVR42u3Yf2gbVRwA8AjdaCfI0FSCpNI/wggjlvyRyQ2C3krAdEanc7Wdy9rDDreaMjbs1lO6eY6sDTSUrNZRtWhS1/24ti6stLSha7RBiz1G0sYuhbpdbaCBZnLY3jzZWc88j5Kl6eXdVUHF3hcC7+VdPrwffN97UQn/2KPapDfpfwV9r2CU7S5qf6QV/zhwZf5WEVeV3WbFsdg78+xk8wR76/PptrnCZeov0izZU+hgy3yZse/g+3xEn2710/PfaPq2khWZMfz0dNuDlg3RKyX97jf8a9l0nDx5p1MQ7r/wdclaNB3XhRluxaGQZsn3SqVZMV68dEnXMy4Ni3FzB3dfAR131FTD4DKfo8DZfGHk2lcwvH90KSCTZkk58NFXnc0g2nfBaLLihm29nquy5xg+1GW+Q3kiDKKzBY7f3JE951n0YDkcfsl8lkvTTbWXQ3B8ug1Cc1W5VvVq1L6ZhkG0LcLp68KvyZx0TyEcthmIZCZ9fkpOvyebc9LHA3AaeyoTBvFJKZweeDIHfa8ADqeSiTGbbtXDabKCiUjSo6wc+iyWTTfVyqFnOEn6yryM1c1kwyC6P1I62xn0xTE4vf/R9Wnf23D6u+ck6VZ847R3EE6PbZGku05vnP7iIpyeYCXpfjec3ouc+3A9+upjSjNaBh3Ry1nh7+7Nhl035KzwH0sl6QfP7N+lPI3KTaU9X2am0jXZzOWB06+fz6Y/+x5OB2/nTKRTe+QMOT65drjhBwayYt4H2TTPbFO2W4P4FIXDgQHofj03bDsDx9/RpWH3HTl9TvbJOCAN58Ppl4tXDwtNtXJS6O0+mcfCzstw/LWaD34DsPIECjkMdxfB8XKX85WuajgcOfX7YUVXgPHW8sdz0w1vLRwNXYUdi+42bODiw5Jdp/cdXJ+tqR7ZKbZaaAkMSKWQyCmp47+M697PnpGd55IP5zjs2wtPTBxaKXm41WIv9cPg8TTat3Vsy92GXw7/LZdcrmph99SeuWGWlG7DU0uBxd6lQO5L3n/1ap9IhEJK3/F6h4agNMPEYjTNpQ5w0SjP/3n5izPM+HhqQHmKYhhB8His1mAQtIjFwCdNJxIUBb4Ph8EbHAd+IZFYree42VmC8HqhdDCoVptMGo3ZrNcbDKm9zKXVGgz5+YKAopWVVmsiYbXqdBiWTNrtFotWG40ShFbb2BiLaTQHDtTXU5RajSAajdcr1ofDarXZDMoyaJOJ5ysrcXx5ubiYprXaWOrZvl0Q2tstFjDUwSCGgWHPy7PbjUankyBwXBBwnCDA+3V1BMHzNhugQX26LIMGPywOEIrSNIK43R6PSgWGNxo1GMKpx2RiGI7T6fz+UCgeF9t2dBiNDEPTbjeCUJTYa1Dv8RiNYhlKh8Mu1+qyOHECzJbNhmE2G8/X1aHokSNgdjEMQeJxv99qtdtnZ8W2HNfYiCAdHQxTX2+xHDs2NCTWgzKKgvLm/2ab9P+L/gM0nBMV21FYiQAAAABJRU5ErkJggg==',
 		),
 		'visa' => array(
			'machine_name' => 'Visa',
 			'method_name' => 'Visa',
 			'parameters' => array(
				'pm' => 'CreditCard',
 				'brand' => 'VISA',
 			),
 			'not_supported_features' => array(
				0 => 'ServerAuthorization',
 				1 => 'ExternalCheckout',
 			),
 			'credit_card_information' => array(
				'issuer_identification_number_prefixes' => array(
					0 => '4',
 				),
 				'lengths' => array(
					0 => '13',
 					1 => '16',
 				),
 				'validators' => array(
					0 => 'LuhnAlgorithm',
 				),
 				'name' => 'Visa',
 				'cvv_length' => '3',
 				'cvv_required' => 'true',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'bcmc' => array(
			'machine_name' => 'Bcmc',
 			'method_name' => 'Bancontact',
 			'parameters' => array(
				'pm' => 'CreditCard',
 				'brand' => 'BCMC',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'Refund',
 				4 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEcAAAAyCAYAAAAOX8ZtAAAEvElEQVR42u2bT2xURRjA98Chhx56MKEHDsRw6IEYDsaYyMFDD8aSaGKNEEmsSgIaDI00wSgJDbYpmiqGojSAilICIgRSbEoqpMra1DSSVaiu2wVqacliSwOpto1t6ef8Hvk2u7B/4s5295nOl3zZmffezHzzm5nvfTMvGwgYEZElRrca7TQ6uEg1ZPSA0YqAismUGA2KE5Vpo1UKp8nxeEDGjJYBJ+pYpJRnAo5BWnnHwUkvOxwcB+f/AmfihMjNTSayWClydXnh9cZakalen8GZHRa5Xinye6D4GjEx72zMJ3BmTAB6pdwfYFTHd/kAzpyJp65V+AsMGqspMpx5E4kPrfYfmKLDmZ81zq/an2CKDufPWmun+e4XOyVQ3ZqzlqxrlTvhx3wGZ7zZemQPt2+zAoP+8XOVz2YOcYwlmO+DG63B9ARrMrczWldgOFPBezGEBZho6HlrMEfO1GVvK3sgmEc4/4RFBsqswIyFKz0/YQNma+tumb+2In2EPLJGZPJc/iLk3shN2bT/gtTs7U6pjV91mCBvmRWYycgqWfrKJ1ZgHn/7lPw1PVO4vVXDyYsZDaJDUxG7IG8m+rA8sqXFCsyKzUcldnuysBvP8g1fZnxVjvY/YQXmbnSpPF3/oRWYh14+JOGR24XflWcy6re+Kisw8wOl8vqeJiswpes/lWA4thABSe5wzna9aB3kvddWbwVmyQv75UTv1eKd56Qz7I2WBqn9uDFnrW78wPqV/VHHpeIedtl2YKG09vOe4p8E+hFMdXOXP45J/QZm9fbTMj0z5w84yzYe9hWYPAZ59nCIjjGqmFAqthyTvZ39Mjt3159fH4g+B0cnCq5jE9Puu5X7buXgLCI4T+5ol+WvHfGU9AJHpdm/DRqnHBq8lbMvujw07vmyvMU5HAdwbsNbg/zB8+EkR50u7uC1m+oNM3zr76TrlM9UR+K9tgsDng2HuiNJwKgzlQBRywOFsvXHf8ofHMAodfKbD/7g5cte+iwpasVIjNaYRHfN5y6NeM9/c3FIVtV9HQfO89StdTz7/lmvIwpAny1Zd8DbYGrdquRbu36N57HnWM+VuK2VO8/EN6in+waTym7Y911+Zw4nbRja3X/Du4dxKGC8nXroerwDPE9sQnrt7nvHknSW2cczdFYhrN9zPg6CZat1AItZSpssacDtOhXy7r3V9qM3a4GgdnCug60InWdgKM89ymK32haN3ckvHH5pUOEwGhips0SNJK3PkKZjdIT09qN98brZPHKNgyqMVcO1Dl066vN0QBLvsWya23/xjnH1YA4BFO0mSkGWFYZoZ5gVAMoGRw3j2fvhUC9ONhc41MGAMePUJyJcKwgcZgaN6xrmXFmXBKNGY9ng4F/wCcw+ner4B+6vaeqUlW8e99L4kExwWI4KmVlDOfZ/+LVEONjMcsRuljeOXeGwjNM58P8EJ9HpMhqMLA2pYVx/dNtJ7xdgCk2PLjHwqYYOLw0wQGkZBOdOG1o3EBWAOleA0pa+HcnrfQDShvob0jrL8YXkqVsP39UF6EvFBYEuQnZwHBw/wRl2HFLKc8DZ5zg8uKUzWg6cMnF/DrlfXk38z1Wp0Raj4UUMhI/t33JKo1z+Bdi9D2vwm4o7AAAAAElFTkSuQmCC',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEcAAAAyEAAAAAB7pEXyAAADpElEQVR42u3XX0gTcRwAcHsJI3yrCFpPvokPiS9aIHYPebXYGKSCcDDwD8QN1JWUBqJhbQPRQ9gRDSOlYWkjQbAuBXOgNnSb+Wcucoq2H0mO0TQYrrFffpnhZng3dxcF3f0ettv97nuf/e5739/vMvA/tWXIHJkjc2TOoVtwaS1nITZXn27zMd+vSsSJ1H28P70ots2sRZAEnJ2q2W/iMdC+TIrm/AjNb0mDmV5cfSGSE2v2HpMKI5oT2/adkw4jmrM+IJSc5lME4mtk0UxAIs7GJaF/21fPjyHQZLVEoxNcEsK86RXCcCeSz/j8Ok3O9sLMGj9m4pYQ5vm1g+f8XghT4oTXXV5+jPM4WcSPaW+Yv5BYlT893qpLqSp7cphck3+/Pb34oZof4/qqmePH6EbCirTmLGtmciDNnOs6P8Yd057kx1BMsCDNKfTGy+QH0xEQwPTWC+SMpmm9OO0ZPTnU2LLAbTrzQMWPUTrmB0QsMBJDvbosVPYe0fyYK2fteaLWO4nBHt42WvnanbdCD7ctIHL5JXSBozSzT/RqUDpMaxprqT/GqS2MqCXglI9Lg0mt7AlyPDm1heIoWuugIeqU8E0iWLBhTLeFaPk963/l6HUVVAWl1x21nh7cos7lTr48Ws3aMKZUdyjG5NdaCTTsjqd1Yv0IK/afmc3s+PeIOrlHfG+0ZXcxSsdhm9n7x0M0HN8wEqhnOyWOyQ92AnVNYaxaidfXqJOjoZrADO0qwfg9W1MG8KjT5IcezdURNQDgV5K153F7UytHD3XDp2plbAiiNrTCtDpRtbdC3ElxdHQjJDvbiDFHc3RrBoGmT8MFTP5BA4Ha8jGuKdNaOdqeBwQDCQhbAHo0Vw+7SVavi6j7nhHI0hEsWM2CGJomisG4fUfpGHZzu+Mz2wjRUGmKHIpROoAzUWXpgDGBkASCXwik1wULCPTkHfQ2+wi0XoxKITj0gNsDuQd/JL4Xovs9TC4s6jDWNOl1ey9JR79ZTC5cpi3f0nGQA8EsHb84q1nLnYdzzD6lwxaAXMRY6UiDU1toC8A9tmbCrej39Gwf5ESdqhWKgWEfGyLQPXWll0BD3ckcex6QQ3Slt3zcVRLn1BaSrC0waAgrgGMgE1P8EE48eZUOkz+sgGAEurl76mgL0GCZSbJ3z2M826jXwRGMu6ZUK9A76gQAJCzFVHrhiaQY2OdokoXMIVkY89YMklU6YCEPKQAPi1yVZY7MkTl/d/sJsppMHH7y7ecAAAAASUVORK5CYII=',
 		),
 		'maestro' => array(
			'machine_name' => 'Maestro',
 			'method_name' => 'Maestro',
 			'parameters' => array(
				'pm' => 'CreditCard',
 				'brand' => 'Maestro',
 			),
 			'not_supported_features' => array(
				0 => 'AliasManager',
 				1 => 'ServerAuthorization',
 				2 => 'ExternalCheckout',
 			),
 			'credit_card_information' => array(
				'issuer_identification_number_prefixes' => array(
					0 => '5018',
 					1 => '5020',
 					2 => '5038',
 					3 => '6304',
 					4 => '6759',
 					5 => '6761',
 					6 => '6762',
 					7 => '6763',
 					8 => '6764',
 					9 => '6765',
 					10 => '6766',
 					11 => '564182',
 					12 => '633110',
 					13 => '6333',
 				),
 				'lengths' => array(
					0 => '12',
 					1 => '13',
 					2 => '14',
 					3 => '15',
 					4 => '16',
 					5 => '17',
 					6 => '18',
 					7 => '19',
 				),
 				'validators' => array(
					0 => 'LuhnAlgorithm',
 				),
 				'name' => 'Maestro',
 				'cvv_length' => '3',
 				'cvv_required' => 'false',
 				'issuer_number_length' => '2',
 				'issuer_number_required' => 'false',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD0AAAAyCAYAAADvNNM8AAAE9UlEQVR42u2ZX2gcRRjAD3JXIkSI0MJxt7kd9vauJwk2tQEFA55UiKFilIq1Bg1SqWJaT1tq0FQiDXjiIUqPNkKL29wVxD+Qhz4EDRLTFluaYKp9yMOlvUrS3eXykIc85CEP4/ft7YaouWSz+yWndD/4uLvZmbnvN/N9M7Pf+HyeeOKJJ57cpzIblgU9FD+gC7GUGoqnNSGWUcOxXlWQO7Vggtnu6LtSnT+ntgVyWrc/r/XVDGpZ87MLylt9o9xfVdAiY/VaSO7Tw7FJLRzj62gR6vWXGAuu1lfNoN4BUCP+nLYIyiurPh/I63kYiORWw9ZqgtwDEPM2YP+pi9A2W9rRWId9ofE1Oa2wNmhFHQ4os81b48b2ZnZNvcse0fd8dvOiQ9iVuhQYVN/cNGCI1VYtFNfcAv8pNvEvX/mKv3NsjO88XeAE4By8ZYA83ktCVAaD59wC32vYyU8fHODHU6OGpt77hUfO3iEBhzUhTQcM8QcGT7kFRv2m/cNlYEvfPn6ZP3R+hmbG82onCTTE8BcUwBO72v8FbOnzfRNEs63P+5RSkMKtlyigM69dqAiNys7cpplt2NtdLl7yOQrg648+tyYw6osf3SCabW3Jd3FWcATMfUk/GLxAAX3+hU/XhUalim04wKQcxnJ8LwUw6sm3LtmC3v35FNVKPuJ0X05TAN96OGkLGLX91G90Lu5EwGCFAnrssZdtQ7/6wTUqaO4orsHgUQrokSe6bEMfPnGVDDqQU1uqBv1j6+tVgXb0JqYL8tD/eaa3KWrCyUwPUEDfaH7WNjThXs0xIbFx6FC8mwL6jrTbNvRTn/xOBT3jbPUOJhjVPr3eEdRS6cw00VFUPefmZWOSAnro6XfXBca3rdoLKtXhpM05dEg+RAE9LbXw94/+tCb0k+k/qBIKBVcJBfP8XaAA//aZnorAR46N8Qe/nqV6p97vPk3UILdRQN9lu/ipN37Y5DO3dqVxR2MdY6yeIpHQT5VIOHH0578Bd3w8QbdiK6UgAsuMPU6SQQGjv6c6i1vgB3uv8weUexTAC46OnbbiW5CzVOAvnfyVBBgWruK2nN60qblvXNEdJvqXE/545bP97PQef16fdAl9yXVObENXOkIsY9xY2IfFXJuCebfljmBrMe6tIB439jIBg7XVVzsr08N4SaeH43m8r1oliTgHx9lhvNjD25GKHQE8HigwaQ9Qt1YBnQMdhzq9W3KV4yDjsh2PsEWX20atojHUqt9UerIJAnssgz02+Z81MMZYc7Qh2gRGdiRhOwN7g5Io7sPvJkA9/N6PIFjXLAtiffistfqRIpG9UoPUgicpOcLSUZENRRnrMp5Be2yLis+h/ID1f1WBliKsDwycN4wUWVES2aQssiksRzgoW4xGmBIVxQUsMwZJZBr8zoKOYx/m917QjNHGqM9Gsb75fAn7L7cVx43/jLABGIx81aBBUzjqCGPMNMwYGi2LYjcaZ9brKQ+Q2A9awOc4EOglWAcHCqGxH3RtC9iEnkOvAE1Au+EV5TPVm2nTDXGmrZg0oBAIweA5DgjWBaA2wxvgXIxhYLpvJw4UlmNbc9Cu4efKfg3XhgGz2sL3K1WBxtiSIlKrOfIZ/IzDdoUzawLtM12x04pDjEl0YYxVI4ZF8RC6OLbDNljH9Ips2YPK/VprCLo1PmNlSVjtPPHEE088uR/kL0AODRA6ZWjXAAAAAElFTkSuQmCC',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD0AAAAyEAAAAACaz1CjAAAECUlEQVR42u3Yb0gbZxgA8OJg7Es+xA8lZKKDQBx2tJXqMdBiR2a6Kv1Q6EBjd8PRSzdLg51amlxC2sqscqYcom2NzLmU0i6T+a82iBARdHXZqFsqWZSu6GzVRk9rQyR2Sd7laqUmOe+5mJaNzTxfAveEH/e+7z33PNmB/rHPjm16m/5X0EzznXdsMzc0100dtUMWzzxXzurCxMpIiV3aPWmX3k2f7AllJUj7qHabNrlYHxmaUmvLk29fZrmaWquM+aR0Y1TXf9/5wLFF+tlw94XjWDS7Hnhv20G/GKEHDpM5Et0Ybd/MXIubZppj7zY6yrJvjm7OroVhjyM9Ltpt+bwPglW/n76pO6S7C+GktCuNe+c56DmrmgLhgtMmrSMcfwrB+xoF0X5xhRmCi/UnU57Dz4N8A8Z/tQigLT/BcOmhl7DWodsH09X13v0APWc9JoHpilMb6TB+C8a7JwHa/B4MfxYFhyME04Y9y5/w0MHC0iMwrUmLoQXt949NPPSYCoaL9WdCsbTuBEy3VvHQNzQwjDtj4TAtEbLkPPTVXTB9vIyL1iKYJqWRux1BVythmhjeOv1wKTE6uHU68l0WQV/64PXetefppvTXhIBK1spJh4TQqwub0n0MTB/7iPOEEzBc18Vzwj3zQp7rilMcJWUvTP/QwltI4QahWP/Fya1Vs4kVXnogU8CSv3n2SdRyn4Nhkzm6YYiig4XlQRgvuxNBd5Bvw/TYL+D7+jc3TJdoq67FV7/NuA9f9oINkrVFSLNw9q8XsEHI2fbuX/aOSgS0hfTHQmo5i+t26log+LwqsoDy0sHCtoOCXiT1MExVP94b5wgwkLn5ALA2BHTUPrzd2MkPW3pjezIBg4+Pum7CezkfL8nVXXNWNieUNVJS18XNNgKjDzDu+cVDlianpnS9WVRTte/aZpjmjTmhrImVrrSGt9bJr2ou7xtw8Y08cQ65Tyc98z6KP2cpbylPyIz5Xxntp2dHUl4p7ZKNK/uTAoSHsacGCISWvTbJ9KxLFn7HMf1J/lU2Z2jFKffhdXp1QzuDkE3ikrlkPrxngf1dAjSN7T6jbsg1FOQrBmnMw6R/WZmT4aYxlyxbaxQdXkTIKGqU1iR7mMqcIorGEJKNqRtcssOLNEYqyu8lRLcWBohsrYdxyosoy4ekAqErB2jMtDPvURGV4R5XkgrFYE1ygBhJYWGEMv/wr96f+vQS+/39qYRodhFzDexeFlHjygx3O5OtpbFBVJA/KrGnItQpdsoL8qdnnfIjF53ytVwfnveIvXrUmwDdn/RzLkI1yQgtqq4cQMieSio6xewu9ixU5tgkPvw7pVG0qEIoQNCYURQg2Fz2jJTfM4qmZ+9Psde2/zfbpv+f9N/ed38ctvffyAAAAABJRU5ErkJggg==',
 		),
 		'postfinancecard' => array(
			'machine_name' => 'PostFinanceCard',
 			'method_name' => 'PostFinance Card',
 			'parameters' => array(
				'pm' => 'PostFinance Card',
 				'brand' => 'PostFinance Card',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'Moto',
 				4 => 'ExternalCheckout',
 			),
 			'supported_countries' => array(
				0 => 'CH',
 				1 => 'LI',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'amazoncheckout' => array(
			'machine_name' => 'AmazonCheckout',
 			'method_name' => 'Amazon Payments',
 			'parameters' => array(
				'pm' => 'Amazon Checkout',
 				'brand' => 'Amazon Checkout',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Moto',
 				5 => 'Recurring',
 				6 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'belfiusdirectnet' => array(
			'machine_name' => 'BelfiusDirectNet',
 			'method_name' => 'Belfius Direct Net',
 			'parameters' => array(
				'pm' => 'Belfius Direct Net',
 				'brand' => 'Belfius Direct Net',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Refund',
 				5 => 'Moto',
 				6 => 'Recurring',
 				7 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAaEAAAAAAJoxscAAAFQElEQVR42rWYX0gbSRjAE5D00bwIJ33Ii/diX3xowHJvAaESci8KJ4V6wZxJ0csJQhTSICeVhVzEP1gaCXXDSUA2YEA4OAIi2QsRIQQC9RquCMXTa04CEjCWEG5v2+nXj5n9o80aM9/L7iQ7M7+Z7++Y5I/tnx8SL3+xXS/LK9vBvydlTasLadvGSqyCItR2ncce2VA7Gklk3nQq+xpS2sbx02OBrhfD75ahTxxMlS/zckvNJMvbwYchR6QZGXiWeKkeYNc5atXKfPzU0fwinjwYtXp6WJSz/tkZOpo7RrYwP0mew5YWgYvzzcGiHPQrB1ga0gMetY6nSn3NLgK+oP+XdkIR5WhCTZZTZfI0PdYicMBqDNh7XzkAx8OSJrLTY0TIaYFMlZpVPzVw4RB6njwIWwD9aESW0zbyFIq0CPztT8aAHZH//tcCB7rqAvacOubj9FxuAhwXoSebBC8BvdXcQu9Uad/dIrBRXEfk324tMMcrHdn0mBH1UwOvh8i73yW3obUF+KMj7ACIc7+yv5qrRC+C1wMfe9yx27BWOid6eQ3ww1DAqg1IT83OgFHgPQEgKlEaZlLlqRL0zlWzSWlHD/jcHxcnstQLEK+w0LsngN5siot2Ys2wLWHLeki5eW86OX49RD3H0UjYAqNNZIUamIbpahTaLiQ2cDUDvLECS67m9MIMhBdq9RR4U9Tz+O4YGWffTZ6XhuCbNR95+12RGYCD23XCm1ADTUGZnSGjMMCPnl+tFj9+bQT47T2YCj3qZT7QpcVYtGuBIfSoxdNDNgdcGSo6eIlYRWsacZHVMVbmqqoTVsdYbK+mvqTS7tieUOojkk3GRdxZcRD+k8jgwle9u85ExtMD7/lJNfBlXhycq+ICF3pJ7pbIgHXHKs0BQx9q1Ko3bdtK44wXQZXT+m5k2qyWR8+/7LT0BJWvIY2nwCJRwUt9CKTntOoCqKbfxdq5UWBcA4xx7CHbSLK0W/LSeoK2dDSifGe/guWow5ISrRXgQBfdNEh22wgcNINbwvPcFCtRFExIwYu3AxgNY2mIDUqasPTUrA1LP18oiws94PHUwQnYcDEn1DC93FhhgfWkfcAHJ3QWjs9P4kmb2FroqrB0/tXAMyNeuhJFr0zU6DpgSEzaAaz2+LMzMD4D7Pnm6rDkvW8sDiNkqnwdsL7Tui1gMjNrcCSSqFS6OK+P+9pnNNOCGnfUuh6iwGFLNskKBWwXMOQEcRGDEqmrVU7r8R1tWHp8x3guLct+F06OwFfXx+0E/mSS/kU7epU2FQ9n/TDBdgcF3hNuE3ihF25WWCwYZyutkxwHIade6G0LsLQTtsDk5KYKF7JopzFR2tl3YxlwM2DIuj09mMyQGy+2im5IhcOGROcLmsF1mVj/e3NgmkXVhcIhxkDcf7ywWfMB8qkDpi/mbg4MpQTZaqiYijk4Q3cMQh1Zg98F8LK8c4IrMHzF03zi4Y69vQf/KeawbyLL8TRXJj78psB1ATaNnDLH0zuwF8PsmOQoOJ6WLoVD019eo2esBEZ3oMXF3WUvBFjxu6qKE6YqDnVR0KwE3kqDUuL7u2XI0bUlIN0epaz5Pl3T/jEw/H3zuM5AVWKX8Vu3Hm6g689O9ZWAcnlBM035iDKOp6iFg7puiqqa7a7aBx97lFX2qpdadEPCGg0PIJEhM5jIj++lfPd28NdEM/Lap74wLxymyqykbfSslFctaduql+M5Pi4Wc2wlVIlmk8rLoFd3s8m6xquT4lPZ25DEwTUfGROLSLadOrY7Fu0cvzSUKp99Ln0/ADT/2zQO5rmsAAAAAElFTkSuQmCC',
 		),
 		'cbconline' => array(
			'machine_name' => 'CbcOnline',
 			'method_name' => 'CBC Online',
 			'parameters' => array(
				'pm' => 'CBC Online',
 				'brand' => 'CBC Online',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Refund',
 				5 => 'Moto',
 				6 => 'Recurring',
 				7 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAEqklEQVR42u2Y6U8VVxTA/TeswRaXaqsVq5VCrB+a9EM/mBjj0pBquohLa9WUVG1NqFGfQhCl1QKCQYoWBVusDVpRK6UuRKshKMYFDKgoDSohxH3LKb9rzsu8eQuPvqGdZ98kJ8ybuXPv+Z17tks/Efn9OZCl/cR2Jc7dIP0nr3S1lFfX29UujoH8b0AGzSuUhMwaSdx0Wsbl1slrq/ZL/JyC6AF5YYpHRmf9IRN2Xg8oozwHZcC0DPeDjFxZFRTCC7PmkLtBXpqVJ2+VtfYIggz+ZIt7QcLZDZXX1x1zL8gbG0+GDZJUdN69IGSocEHeLDjjXpBQ2couCRnV7gUhgMMFGbKgxN3pd8z62j4N9H8NhGJHRQ8FET+7wFR+5OXPt8uwtB1eefWrXTLi60ofeeXLir4HiUvJNvUDV4l7f52PmxEHBHVS8QUZ++0JMwalwq013gzX/b0jIAPey5T41HwZvrjc1ArcJ3Fzg4zfftlnwfE/tBgLDpyRY2tbVsvQhdtkXH59rwAcA3nx41xJ/r7xHy2evLXJpGOU6O0OOA6CD0eigFMSMQjuxCT/tXAMiB2snksQnmWWVsuJc1flanunPHj02Ixtu9Ulh083y0zPDjPOs/U3OdZwOaQcONko83N+9m3/p6+R1KwfpbL2nJy/0i4dXXfN/F137pvfW/b+GTlIelGV9HRll9WYsWWH6iWc6/GTpzIm9ZtnnUD336ZrN0OOx3gRgczNrgg4cf2lNqM01m3vvB0UpLDyuJlv4tIiP2U/zCg3O8Gu2i92hN35qeaMWStikOq6S36LLNrwi59bTPgsLyAIv6eklxiDAGw1BONTVpQGNBJzWtdI/vS7yECICeuFMnFTPUGDMBzXwsoKTlz0ZChHgn1jxVG/hUqqTvmMeSet0Fi9NztCEA+bkSUJH633mx9XwxXVYOyOPTn0GgQl79x/GNCyKIZCoYJdn6PwqYvXfN6pcvuOXwg4P+sSG44EOzJpWbGPNZ3IWqTvER9kP+ueuy3f03eOgOhiBCautvvIWVMPqB8oQH1Rn0/LrTTPgknpwToTAwphFeYg1eO6JBnWYC0yH7EUq+wxkBhIH4JwT0ZSIR1HJQjFTHsmrfLa7EUlCM9ImdrsabFcVvirSbmaTt/9YrMRqvaS/D0+PRLPqfC8oxjy19oVYCDe63gd9/aiTc6BMCG7QvswJCXTvCPf605RU3imY6gDnCN4x3POK1xnm//ydrtAan1hDK6rhQ9oiibvGI9RHAFhMs4Q7IDdOkCyqCqjyusRgHsKIWOwOgWR1kY7gUAgNJbMgy68p71xzLXYCYCwFu84tXFu0B5KlaEiW7/lnrMFVZp7QFThYCBAAMtvROd0BIQ2BRDch/6Lpo7O1KpwMBB2BJhAIBzO7CA5Ow+bXXE82LE6FkJ5dgQXwdXovVBEfT4YyLTl24xr5e2u9Z779RitsdDS1mHm1ETCPTB0x/wvICIQMgcKIUxuPVQBRMZiDPHA7vCt1hr91joexYFRENyVOYgzjGM9d3AmAQAj2E+MrqjsVteK6haF4wC7Euu1YiBRBNJildYbna3NbR2ultv3Hlyx6b32b+PMGJfZ92wvAAAAAElFTkSuQmCC',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyEAAAAABrxAsuAAAD1UlEQVR42u2W/2saZxzHL/0vQjz7B2y/iUTc71cSkxihTapwJaPjNB7Yda3YsE7riY1DJi10TTLv4g9BCAQCPXWHwx+CUAiIipkQKGOWwZYLB2kDgiCS23327FATU6/bORit7198nnuez+vzfPl8Pg8m/wc/TP1DcgSur3569IFDnMb71nAmNH3n9eLjkUCuHfhrTzJdfSlPpXWH+Bq9CNBXbp0hNwyJs/OQJxlXQFfIxXWAHtzUFfLNg0GQ6BtdIeHMIAiT1RXSf7NU3fPoCnEFBkHIgM5XeMX5fsf+jyBT6fvWfsTCG6fRaVw6+1wGuX+m00jUwXtDZj+9YSADcwG0afc8THbV+nWRDFAHgyIHtGrVAJl+u2D8wuZrrDgjme8+QRPjr+m0/fnfCebtre8f8YMBmiDXJ2I3L5/+7VZYMXHZCjRDnMZ3G9CioZAF46r13yo0/UFVRpJLlet2MdQOyrLEVyoPawTOJmuGfu17Yl403pZijotHjdKpSZabVKP0wjwUsjZ+/kGzhRF4nr340OmUXbQSO/RvZ/39YmgIJGrpDn7lzrP7npP1XsjuBsn5RNVs6EdbSuLV8aem4lGh+so9FFJKqu04p27G7atdSJ71Y1HLyTpyQslom12XbCk0YykyBFK3o9bJ+tR87yFe3K5CFeDM8XmnNBz8dlhtZydQz/KkHxu8kiblEBaj6niJ94ngmC2lXohLIcuTrZmutyfrTar/4OH/YvTQjFpg7mWkO741I4Y0HDyB360gP4ffrnbw+rFSAubPf9MAgWkrm9vhvbGaoVLJs6ky7H2CybO9EmbjHCCQbl9dG89OlJRY2hvb3WCOPz64/3cQktvCQMuTI4T4MchPEPeQ/EYIIZXLCckPQvOZKcHAVaVzdM4hPB1DeYnORS0OIeZ1CCgfuOioBfqhj3JogsS8uxsSP6dUkewErKpSIXDoKSUbpbqdwB/WZPmXPyD7LkUgZgh8C4MApHPtYJ6V+KdjGiAS3yk/M6n+xLztIJgB81AICFyYbQdddJxrUpAJupBCtW4nuTx7aNa0XXM1iadzBP7CfGqCPAVmagb0lcCLR3uKr3EODPdC6vYmJYbEEIzUAJmal/jdjbuV1owthQz3QoTZ4lEXsu9RIWl7oar54A/NTao1Q+dcdKe8Hd73wN73QgJX2sEdN7wCoFjDOfz+Q6cM16RTLlRfRtjkEIhD8GN+JUpQyaJzCcYhRC22FMlB5MBX1L82vvNXmZ2rJZiY10WjGuIT2WTgilohdYh4tF0jTisrmzvuj1l4tBDpV/EzfdW6o9r+E7n/lNAu/ddiAAAAAElFTkSuQmCC',
 		),
 		'centeaonline' => array(
			'machine_name' => 'CenteaOnline',
 			'method_name' => 'CENTEA Online',
 			'parameters' => array(
				'pm' => 'CENTEA Online',
 				'brand' => 'CENTEA Online',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Refund',
 				5 => 'Moto',
 				6 => 'Recurring',
 				7 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'edankort' => array(
			'machine_name' => 'EDankort',
 			'method_name' => 'eDankort',
 			'parameters' => array(
				'pm' => 'eDankort',
 				'brand' => 'eDankort',
 			),
 			'not_supported_features' => array(
				0 => 'AliasManager',
 				1 => 'HiddenAuthorization',
 				2 => 'AjaxAuthorization',
 				3 => 'ServerAuthorization',
 				4 => 'Moto',
 				5 => 'Recurring',
 				6 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFgAAAAyCAYAAADY+hwIAAAEN0lEQVR42u2cr1MbQRTHIyoqKhB0psNdcjt3exlmQFRUVFTEIRAVFQhEBQKJQERURCAqEBGIioiKCmQFAlGBqIiI6B9QgUAwA/kx04gKBH1v7xKS3F12b3dzzV32zbxhIGS5++Td2+97u0upZCwbq5VqzzxCDt0KaXiO8wm/Ghf3ETMCFoFb3aiuwy+04LVNE2ryBvyeA8djCNS9qRdohTThxTWDSI9hNHu2R0fU1zC0DRZ9hhkBmNbZN27FfUcJ2TFYtEfxSZAeCKmhi7ypW6bve5Z/3rPpDfhjgf0h5mc3fZtegdcHAul0nBVEAA/IJoE/0Ck4VHG3vOueRd9qAXxfdt/AoLcGbDTK+5a/qwQYHwX2aRmYST68G6kFGcA92z81EOd716bfpAA/QmUHA9wbiFz/GzfpcQFjEjfwxLwPUjc14K7lfVzAxdwGM3DoNh2kyXdT7xV1nqQUH6cdyrSI39u0piTTFmnXUL8HSsU/gpu4TNCgCOKrzPj9sredCLfsHateP8LF+mBpAc8azsoIMwJaEnCvTHd0fmAzIuADXic+7bkBPFU1Ylp4AnIiNU58qmvjk6MGlx6MgiCXgNnjDZPHBOSGJIjG7DxwR8grxcg9mpJqeQXMIFv+oUq+hBTRnJRUvBKXP563F9HCeQYcRmE77iaE3hvk80QQMjm3cIBZPobIkYy4H2HF9VnxSdpNUjiZAsbJA7tvkw6Ppa0yJlaVKOWko9+mFziGprng/wIOP+m4CxmAKD9TuVGp6Ae4dy+3XkinhaCiHc7tR2QJmFcBqj6qae3PRnVdWpOD2hBp1WYbwTat8/qoWUUxNmFUopc1vMIcvjwRDBHKieCLrKI3XIlpKVWWAlGcKeBJWRTjHVWRn7ZPwLpdc1YeUozzsByAscpByJZ/HnabvuPkFtcQSXODKoCxr62Si3mprwA62PsiCWV/3LOFD1qHIikcYKye8EnQoWhUKzmcNOFafhcGMBYteENJ615pmzKoZ3HiU0oVQX95WAjAmLuVesHRThqmiitVmRj2JPINeCb6dLUqta1kTI6dK8BhWjjTAWSmVTm9Emz5r9UlKdtGlh/AKOVEJxENmvyXaqpglZ7l/1xawKhNcc0srP4SV39ldTAHMPql7NjjSg9Kcalle7ZYKLNMLrN0nna5X/zvDjPZGwGs0gM2G0+EPa5XzQWMk01mEZDzDYBxK9SCm/9oywDkpodmHDshwGwSSre9adX8NqmJJKwieOtRq5wa5mnpVDINJ7w4fbrC3u5uVOeeKUytg4P9wqx87bANHAuICG2ycBEe3HdLtK+dq35wHm0M2AUNFzn6aUzZaIUEq+fsELjjnBok+swre9vUcQ6efuA4x67kzhlj04YBi2e/t2a3C7ADzHhSPOFYkrH5hme+8Ugywk1kiP/rwHWcffP/H6T8yI3pqv0DUKie1NAOCkIAAAAASUVORK5CYII=',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFgAAAAyEAAAAACtAZ+XAAADrUlEQVR42tWaT0jbUBzHvbqT7rBDEaowinjUUBGFjh289OzFggiiCGJ7UGSMZoFC90dcLVgcRRFLdTg3ZCCMXgY1ECi1xa4FWeiQSdlUuolSCq5YYp8xS5PmvWSZqcnvB1J4j5ePv/7e9/1+L21gDGYN4M/l6Pqp3xow+a36dECWO/oLfDr4pPHbob7jevFnybz96wbYM3NeMEIyBEzfeyvA5wW/1RjZezr45lEFeLdvxzAb7vWDCnCsJdZSO5TEAq2TcQdZTx8a4D5Nxr3YNl6kxVQgFySA8yncVl9UaXdFswkFwAfTE916wGVjvheWAS7SrqhecIGPuI83kcBvX+oJF/jiFwRw2T4e1hvw8Am/+WqAswm94QKn16HApEvpIhPdrijwsSbpzGNHpVwsllJziIwX431/VUbW/s1K1MF0xDfbxquogwxa4PNzAuBPD+XW319NYrcKzNnxZtDCQaOA005l/xhr8YWhAdKlCTB7Po64Acj7x/A5fNoRmRKFXi/aC0KgITDD0OsAeasTPmOrk9sFZyH0WhEfO1NTYIb5vIjOzLUeVqzEx67YYmbum9AYmGGIDP+IWgtahBDw3K0bcBKLmeGjL44c5EYOvcJeuFpzVAKXqHyK9d8/0DPL9oNpVPzniLJdfh/8N/Be1aE91hTC0Q+F2xxx0YwazyaEuKqBxaeg3NcKs0I7avQsVFvcqgTexsW1qpoYF2l0fMt2kOO3AryREy4zR6iJbz613IGeURtjlcCsHHGO2+RkH1YZOEhhFyE1p1ojVANHfEFLoNWLzeMhnC9HpB+IBh4Po/NYnH6a6/DKV/gY9QwgzOPyWlI34PiCF5PXGrmTrkhPddUFuERNdVV3YbCCZsSdT6FXysU5PdYUOISjq1yuVnOQXkxOFuMLmgOz8VNSXCrrNtjZGgGXqBAuD8IWl1w/fGiSvbFs1Qg4iUltEjk1f/pBLi3Kds+9WwUutKedGzlhJ4zSYSGwg5xtQ82+vspuRrT5aSe8PYc36rArACkXV2JKPO002EUKX13XAJcoNRHQ+jqQ760lcni5Q2/Aaz3I28tCu/Tl0135RHd1oSSpEuKO6m7TQajUEFnLJnhNvUsnMj8bFb3jAHId8eG24RPlkVAih8odty13SNXcGtXD2tk1cNrCvhI1gr1yV4AvR5/fNwYu3f+u//pd85I5bdE/7uWoZ6Y4dPPzg4BpyQxePOvVzgs7jGeGJWy4aUiOPjbr9dcSfuuKfbePg78CoieM4ZUAWy0AAAAASUVORK5CYII=',
 		),
 		'eps' => array(
			'machine_name' => 'Eps',
 			'method_name' => 'EPS',
 			'parameters' => array(
				'pm' => 'EPS',
 				'brand' => 'EPS',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Refund',
 				5 => 'Moto',
 				6 => 'Recurring',
 				7 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE0AAAAyCAYAAAAZfVakAAAFC0lEQVR42u2aIXDbSBSGAw64Z6tj0Jsp6MwVFHQ6AQUFBQWdKThwoKDgQEAU7crpxKAgc5HcgHjG4MCBgIACg4KAgoCAAwUHDNrEnbYzBQYBAQUGAQcCAgIMeu9fSclqrZUtW3Ybe3fmjRxrdxV9evv2f09eWDDNNNNMM820LK21UC8cWjV2WPL2D0t+h45fyPbwHZ37yRBSWrvkPyZQXbJvGjt+f33joSEVtvc/bzwgKL0UYJGdf7C8R2ZJ/lIvwYticIr+14OS34QleF8Xy3iuoVHMqspQDor+axkKPrctfzcO1Xsx79D25biVFPCFN5L3XfSz/LdzDs0/kaDtaDcK2dsI4LxDu1h2tINupeyuWwbaJbSeBG0zBdqmvIznGtpB6eUiNBoMsUu7y5br5ajf4XX/jhFqpmXIAsr+7VHtw7WNW/OWMiE+nQ2RAQww75R03frsp0yUP44PK26UOTybcS+rvRkA4Yg8aBveCJkRplLHAzxuP+2alUrlxgrnf9jM9ei46TjuEud86OXtOJVHGGs7fCvVbLs8GYkhK/u4RmunJeNi17RqnzTgWkljGGN3HMberDDeI/ummsP4W+qzqLsmwK44/J+ksUlm289vTxGatz1MvQx9Qs8bCI1g/E43cjbEzRJQtqyOJ68pELCjYYFNFRoS9ByWeEtdTjrv0hkt2VhcxHLLMn6a0PrKPPCmdtFfonOvCE4jacmG5aSTJGjCQxjv9t8U+0rHXbKmxoNOEfuieejvT8r5kx8kpnmrKjD6/t+E5bunLl9IjURozH2RAKxRr8fHO5xX+73R/VuCdh7zIs4ff79cU4L22Vq/EQNheU+xXBONzsl9IW6ToBGgdzEQDt/T/S+ApHqjBO0/ZZ4jxD7VsKwntiwToHVzLCtJ0JQ447r3deOxnPrjkn0znKeZMaZ9SbtWLtAgMcae61KC6KFRjEubQ41/dqVy90Lbqd422M5pGT+cnKflUBMDrEHQ0saHm0ZPtwM6jvMgq+xAfzV+5hrT1J0TO2W7WFse1oLsYXRolB08VfqfqX3W1tZKjsMZxb8dOt9KsGP1msuMPZna7vmx+OdNCUQWywwNHtUnTVI2DV0DVBrbUTaU1UnqtI56HjsqYIYCtjWcedvamEZSQbYgU2CNpGwhkhUi11TG6QyA+qQLefCkM4JcSztZVbxkTTHedX8bYw6RlmUpBoyce+ZZ2hnxRlvYFMKldjwONPK+7WlVOXrwuDx+5JLVKxDgsdsJzZahoqGrmgD8JN4+ddJ+5HJg+R5+2zFqOXuIm+sGeSVroHQkNgUSpWFuOgqs03C+1YVJNSTiuVdupbiYRXJcmRbsjiPJCp115GU9k9AuSjtWjaGcPY6FP/YrpC1P885vhJhmiOQITQhYygqiqoaBNgAa3kZBYkBqoBiJ3HKca2KuaCeeWWgkQF+FL18WIRVsxl5DgsDrgpyUNaDqUeWAWEXqFcB1n0Ge4HMoUxrQZTiP/qIfPZDob7w2rFarv0ILRg8mKF7yJs2xfrWgOXwLN0vHv1A/g84SwAgQQISAliI4UZUXsDEGN47PAAGQIfACQIj5ON8MC5zNy/yUbGX1XvCAoOvcnR8JWqx0o69M0NMnKMF7BXEjuyggCngEVdTSJMGK76L3qCKDiODQEfDEWOZ6Qay0y+KB4I0WJe+YMwwJBQGa5hg3JMxNE7U58kYAy71QOcvNwDLNtCvb/gd00MUhADFJoAAAAABJRU5ErkJggg==',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE0AAAAyEAAAAABshtU7AAAEfElEQVR42u2YX0hbVxzHO8iDDFsp820+trAy2MNeNpfuYS9jFBmhdTA1yJiE0j86SqStU7Lk9sFqLUYqK3dNJGADobrFsCKVSYgBG1skElKkMUhYuFtsk3mD3ovJDad3OT09u+fGm79rkxS8XzCc3z039+P5/c7v98s5JNbtdegA7QCt1mhC62LDcFuf+dJno0cWG0BL3aAF+Z4RzeeSzh0ODdQFWth9+gUJBvWNZ52pOdpe/NxhhKPzTQqTAl6/nhGhtcZo8x8jlIkQQhFazZ8gy+8f1RhtuA1FlxT6e3GdD9qor2qM9t1LjF9+JW1o3XS+GqMh5zkspM1hqQs0tDtntKRtRoucXGO0Px1BPsjvxUkb/y60xToOCpUy2vMvCylxo0ZoM9pvqdwqkKuuD+baq44WGiiGhfVwrspoN0/IAS74rd0zWodlUsCFC2u4Tf4lu7uP6Xn6vskX2d5SrMmqedqlkYvPlIGGMj7SlaPyYh7k+6elu0PETn0WpU/qtN//jTV+lUmTT25vTXwt3ZWUaK4Izdq9vz8DLZPCfrQAe/5U7kt12uXe/7o+MNioBFYh2kQo32TscowWVpHrRWrVj2a4NMr3K0KTWh/QsvTh7bj9ueTavTiqrwhNAP3H8YsuX7tzy2aSVuji0u4unEOxaHzJ+Rpi7QGHwX56Hztw9Ah28Fy7hPbHdQziVINX3+HW43W899KpZ4No9PTu/9qhCG2nE40e0RMhSY9oZE3ckNCGH6LX/vwF+YX3evE6wtEPn6LRYONyL9aqv7gzFdB6Roo9AF2K0PCaRWU9Cp/B9mR2p9pMypFmvCp/qgS0K0eLPQCTiBxNADkzXkVgbApmPLxuuTob3BwvC614RzbUkYuW8yMR4GhDbots5Esfg42gvFjD+3OdceuVdMFfGG2NQtbzp7AlxXkZu3H0DNbAe/jJ9esV7VD2RwihrPxoiWbsTvnmIK8UZ3gHzfEYykLrM+PxTucD7uaJoY79snaTaE/vIgVYp1qqDTBdhFX4HimPAbt8jSqzGpTa9OTL81A2kyg+sRaaAQuacjNQsIaW1vTkf+noGQGkOCmmlOVoqqC8n34x1178ACbfWtiNIJvdlPsNskNJcSWj9ZnlBzC/tYTdhRru3Jf1H6ey0fYsKopRy+Vr+aEuLlFs4Q2wD+12vNQuF0WjcvJ4I13uTmf+dEGqz4ycXUU02PQsNjgshbXYgJNyVdHKu94ytLAq0ZxM1yGaL8JnQLad9BY5v+QzcB9XFW36mCgyaY/BuuJUJ9OJZqd6eyuy4WgKsG79qj9qceujFqc6xQXY7a0A64vAz8f0P6zdCP+Z5V6baWHsDaG5NClutis2RbHJtKPJqXbrfRGIA3vf6WOzXcvZv15m1e9UC2BhLDZ138RnbCZYVT2GvzLWFSoL+VrQcJND9hR2o1svitaVO7c2x5NplyaygZKrSwN/rwIR4sSmvMzm+DwdVvGZ2S6XZo1Kpn0RAXgZ+iQZCnVzUpTiFsa8DKjPQyzwFp+vHaCVcP0LD+1u09BGa4cAAAAASUVORK5CYII=',
 		),
 		'giropay' => array(
			'machine_name' => 'Giropay',
 			'method_name' => 'giropay',
 			'parameters' => array(
				'pm' => 'giropay',
 				'brand' => 'giropay',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Moto',
 				5 => 'Recurring',
 				6 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGwAAAAyEAAAAADMGb3VAAAFc0lEQVR42u2ab0hbVxTAnfswqg0IC2LBDYclCsLUhqaoQ4YMi9DM4CamtLWODE2skKnDQRdamrWZoywmDSvWP8WUVKwzVKRi1xmaLVlmUwhoiKyIzvBwEAhNPohGSZf15uTkxRhrEszaRHM+5J373n2c3zvnnnvuezfNl6K/tH0D9uxnfefd35NLHv5mOeY9viOYp/ZGe/F5BjM5JZfTuebIjAC2qGR/mKxQKDnS6fEwMEdmvjvZsYhkUYgWAKvpgxMFz0dUq7JEDmrZoUSHpHs6CDY9Do1Vo25dorNVosEYzG81QbAv7/ojVG4/m/g0nHiwAn0QDHKhYPD/mF8SD8Zg2s8FwJgTRP2pNVXAzK0BMFA12lQBM3x2ALZPwKwlRP6aTTkwLp+IQH8Alixgih4it+8cJI83C2z23e6Kdmt3xaOxdTm1Bl6i1sI9NqMGjVwvvdVuVXdC7xn1zdKLBW2l3RXqzqX6cLC8So2WSLWSwWxp/nXIwtZoBYMhCxC5YHDAa+DZKLNonCXOZhpJa6Udeim/w+vOW6GlajRqMM0nMJKIfNPwtAOOrCXhY2x4HbSFq7xC8k8QHb3tVro3l88rVPSsy0PBiqZAa2kOrXdGVFlLYEvH8taHvMIqmiK4uPIovhKoMUSwRM5tiBIMQVAa398NTKCHf1XTZqM4HfvVbdAP50XxdrBnY5EDtaXZ53PrbNTcETwzd4RAD3hBu6iARVbACdqoQ1HiQaCJ8vm065lo3s5gRC4/uS+ylqg7QRN9MaP2+Zbqm1ygTy1sB3vp3cNdpmrl1Dxobl2OHILu1D0MSoSrMZJ2ODZJyTlxNmhbA/GVYBBWXL6dBToatzMYvw+vRX/hyDKkoc+2g3lqSYgR8z210MLPR/OKrwgGxdkXuEMUhi1ptbBBK3jOYD6WgddjSB7oL9RvvbUbWP8cXLnZCA+l3hz0SW/o3baCWdhozAMGtHSZiFatXFSF51MAQy91mXIbvBw6LGMCq9vAIb872GQGXOkqQw/ivZxCaGlybQdbVKExIwEQkhurlWDyqswk1Wgfy0LB0LcmKawfPbV5lTGAYSjq/SCrut2Th06CfevN0PLPn5j4Qb/WGmmMlW/6X8Is4eqdjBdIEV4OW0jO/eAOBaPthIw4di6meezyEzCm3tw/N7x+5tPdkwcNpmrCVEL8PaNuVoT6NBzMRvHza4wGXmjygHHk1pEkXr65wtoKVjUaGqCYZKIEs7PQZ1vTdjRgTiE+iLoNnAS4fIknUrqPXJdgulhhEWAIy5dJKRsNt1FBO8/GXHkY0jCkiFGTGdGD+XwLV+mZDORrmass0gRtYZukdL9xFiR7tpB+W+blnHGGF2P09H3pdBwllVM4mXGzVNHzaOxFMdYhMG4ilVTzYV8BdJLrmeL0Jlezorvij1nw1nYwAy9H/uMvZtEKy8KGfIipfoiyUY7DFvbnHxHLiGAoMpi5DZBAvJyj78QM5hTSx64yCC5e4V4VwTRYPEVu1SiE5zgrjiJY0SPQ376jk0wt9M/x+0Lz2usEO3VvwDtEYaBW2uMAw6KKFn5fqBdfD5j0Y/pOA964li10fQgiTo/vHcfegtFrgSEKVwIxJw9H79MOnWR4/b7IkIZ14F6BMY01foHpOXo5UXqBKzvUsfzqfgev3w7A3jSwLCq1wEg14wcr6iHqpdMp9xmJb4E3R875VADL+4BsjfCDaU9CEz8fK+lkBvvqRPCLpvc4519oLN80SRMLl2gw5gQJxOCuAdtyjjR4ylhprzEmSuDlTeJE/XfYBhaTNPl3emRRg8sRthw5Mtsa4Gt0cgq3zXJsx01iq9ceUH3vff92csmNdu3JlaP7dVtfqvz+AwuJuc7pa4yAAAAAAElFTkSuQmCC',
 		),
 		'ideal' => array(
			'machine_name' => 'IDeal',
 			'method_name' => 'iDEAL',
 			'parameters' => array(
				'pm' => 'iDEAL',
 				'brand' => 'iDEAL',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Refund',
 				5 => 'Moto',
 				6 => 'Recurring',
 				7 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADkAAAAyEAAAAACTJPDZAAAESUlEQVR42sWYXUgcVxSAg0P6lqcGLAQtDXlYCCGQh9Q8rPRBmJfBC4OyVSIskUW3MWXFOiiB/G2yxVbDClvT20INC6us7UJCgikVZou1stmGFCosSdl0E/Qimj8ilbQh9naOZyc7M46irjO552V/5tzvnp977pm7i7s+dr015Ny+7cjrU2UgPyDbk4PMSxpZK+sWBqh6+d93XECaxUP8wog0t2/TyK276HVwls/yjDQpxWibcIzp6Hr2q+QQ0jqeBVOSLCD2E/qwzwUkjvvt4QrPqpsHqF2CbRp5+vu1EikMTg9Oj6V/+Slf/fIH49N53iSgrWuhm0YSZWNpWDzzZerqAitpxOlBZgfdMSSK7OtL/3Vd1/kt6LGxdIeRKLHEq37UuiUd0CyNUceRRAlNze9GvZsSJJJxrzqEJIq/So/rcc1Ov+ACkihdPnRvbtW5k1+4gCTKlUuo261tmE66AXI5GB1vZfXsrJQzlK3RoXhHpNCchKmak6NDugSiesrAt/Mn8VukMDoUKcg+dO7DIMRz+bN1kDnJ+6ZMH2DfUrOtT3p6K4kSiBpLBCB8WXRivhqRqsi5KhJl+B4+9ZHm2pRki/yH11lOh0zxwZlsvhqhDYuIvF87k53JhqYA0ZfWl3BixYhsTuJSzkqwP22RSWo9kI6zUiwDUShr/TIieysD0UBU9umIafqkh/NYwogkyu1D8OykhhSZLRJWY5bDxJg+uRaIqtGxGMulGoBNtHF+96kZOZZG78FcGE0LsoeuPfeNyEeDnKeuWpG9las1tePHzzl/1e/LGpG6y6H0/TFkg4yvQTaaHAufrlyyOnYsbUyxSMGIPDWHv0JS4t60IJeDXgtSLaaPKt7ZA6mwVNOcROQ0VUVVBADYvlSzwBYYxHqiDZHzu+F/+AQDju7UsO0m+V06wkrAAcsm+ftO+D/zJtEjq3wINn3zHufPP0akebS+2SY2peBZMEJlQWSdNGMoBap4491Ywl8FE7fU4vrRyt5KVQTL0PXw24mVWKL0BOq3aVYmqeMFD09Q1Icu4ebXriD1IED9yVy0RX4qeIlR5oPlIbuKVkKGPNhvi2wSzBk7W6aV/TJ2vDDX472uIKE8wD4o1THHkXh8RcfXLes7jQxNoXajFskRyRXkz39iTYNWRG+5HEW2h1beX63K46Wjy1Gk7JvJguYLfpjo9dVhJOYq5wMUbCx17I4h4x36CxGclBODG/SxO4Fsqb37FLUW+VFtjiZhwxeEcpENi7EE9ECYqdC81ZGlPY4h20PXzkEXpLu0XtsaR4n17qBMpL8qEO3ynT+ZkG4fKsGwpYEYHmE5xYUXd84zEi7cL2AhdxT5gn83Xle8kIm/dPR6Is+vSeGKeuYp9r7hivXuQ0zIbmFr0kmbhEbmJceYMfYiGxnRX3gcvd3yEFkIV9z6av7Cpi/UUsPbE/Vy5uKD/Vu7QHx7l6Nujv8BicIW6mI4150AAAAASUVORK5CYII=',
 		),
 		'inghomepay' => array(
			'machine_name' => 'IngHomePay',
 			'method_name' => 'ING HomePay',
 			'parameters' => array(
				'pm' => 'ING HomePay',
 				'brand' => 'ING HomePay',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Refund',
 				5 => 'Moto',
 				6 => 'Recurring',
 				7 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAiEAAAAADhip7cAAAEvUlEQVR42u2ZX0hbVxzH8zRWLnuo+GBfprYvPhQKXrE0MBhcCBacGkYfApqVCWLBgihtUSoEwl3C1UhoSLNbG3Xahqll2l1UBFMsZnOrCabEOl3aBrSNjVXyB8nMTHu2384u58aIWbXVzJgvhJzfOfd4Puf3O79zzlW2eeqnn3uff/vR4dew3PcEIdkvU3p75miuQmYezCTgoTFZJuHq7Xc/OQL+sDI/8+StFYXC+6vxlgMDXhLQAXwc6gMCthYglFHAw4VpA9zWZHFK1dakt3NqUk4cuMkh2o2dW304WDVcKOR0XcSlGyfTFNjidE2Gwrg6WO6aBERj53TregzbplulAx/Nw2sxFO77UbS1NY23RHwIxZmXKy9X4kz0FqQnW3uaAoOEHFzd/UKSxo8lPwTi1Ag9v0DKHbrXJoTWY98f59QY36F++3cbISeNgS1OXC0N4PYa8hhJ7qD1GOmGvwyRECw3OaQteh7HmQeW/xmw3o7QbwXgq63IobDYDadenofaxPAFTbBT2mRgj8LIiEOx9dtPv9vg8dNGprQepDL3Rd878HDhYFUyMgHGSyFYnpyVb5wczUsGdio1ueJQeFZwvRtwzG3rR0iTS1MGxtT4pZam/Px7B9bb71/bGtgE2PcH2N2v/uu2tB3wI6W2rNsSc0NtcGOoy8D4+Yj3Zo/9dNwN9U/vcNmmxiiFkJ+HbwAGUKeSpqy1CM0pDIzKrC3z84uzNWO9zfBMXUCMpF0A6+3jLeIg719LBP7zQnJS2xm4LuBUYmlyAfjqQ1t/xNsXbdiEKdCWLc76dCqztTbivTc/1IVQbzP8noxcfSiOAQPH3dZamuq2rA4Uy6u5bsvnZ2AqK85WnIXQpykx3HcFTJDfqgerCLCY1qTA/GVPnrihBcuhdSJwNSe4sBo2BZdPV38Ct60L+HmenaiE36X1ES/4k8tGqNKB62EqCDAWk+XngxtOpUcx9obJKq1H6GYPTS3O8myxPLixJ+BE5LUisRtsmw1vf6pKHdIeBc/ikpFxKnkWQ9WMwbefh7bn2zW5WD4dATYw1lqnEkI+7q7mzskv3T7fDsBP79BUb3M1d+n2HtawqAcWETl6S+wG5+jl+URgWzs+mqQGjrtNjbhUM7Y6wLM4CUmBxQiY0YDXSUiLvYy4aMqjgKgAYIRUZpW5WE7S4R6A9fbp1q3dTLB4CqwFuwNGyMDY+gPXefabMrAkA1trDUzg+iMlrPHtgKfyaaphs/7EZzIm65+jk4Wmzskhue0KOHFrIchiN21N+FAqPXntBOzn8SrF8AtXICTvzRuYOQW2YC9i/0S8uK3g4rJHXARhopJnRW9jH2vLRlxT+Ty7OgB/gaZIgksJfPcYsbU1bZd/MTKxd+hwipopgUtHKuAP/Vm4YmRoaio/BbCx0/Hv4cL9SrwDWZwzJQgtCVvvRBhZOhHmZ3g3Xp6Hm1L3CyFn8QxcJH5d2X9gnlV8Bbl9R2DYSKTq0IGVlHseJwJz6pmSxLWtt3ddnG4NVGJfrxX9/sVoXuLlMO3O0of3BQC53GYIMEm7GQG88SlJpfsMPFi13y9oQ+ElQXptPXoRfwR8yPTDguy7rzPs36WBaF9OpuD2fvzm+F8pMyiXAomDhQAAAABJRU5ErkJggg==',
 		),
 		'kbconline' => array(
			'machine_name' => 'KbcOnline',
 			'method_name' => 'KBC Online',
 			'parameters' => array(
				'pm' => 'KBC Online',
 				'brand' => 'KBC Online',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Refund',
 				5 => 'Moto',
 				6 => 'Recurring',
 				7 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD8AAAAyCAYAAADrwQMBAAAERklEQVR42u1ZLUwjQRRegeDa5YLgEsQJBILkEO0ulyAQTU4gTiDI5UK4/UlOIBAIBKICcRKBOHHiBILkSNotFYgTiAoEAoGoqEBUIC6hR2e5CkRF773Z3bIt+9udkgx0ki/bdufvm/fme2+mgjAqT1wO6uNCmcwIRZITSreL9POzL8bdMhAuCgZpATp9uIZ3+0LhbvZ5kS7dvgVyFQ/CXmjTRah0xvgnXmguCYb5JyLxB5TImVC4meaYOLhwiTRiE39AhU8PwEkb5CIBcRvNPQ7FjWjJiSPMe6oZnJG/ZEMeUCTfeFP3DkNU+SFfJCuMyXf4Eb6iucGcPDdZoNHcfMHkzVXm5PE8wE1yw5b8FWehzqwxDHX7vMX5LUbk24Lxd47H9PYqMfkS+cnpwYZkfM7u0ZObwo3I78nOSngGWADQjEHDG2aY2NYPT7qg1ANiCCDe9pTJJM3pDXIAOLQvQwDmOTzrFga4J7CgDYcoTtprZS0N0Oglhd/pzSBleq+H7fEz61whMflf/6bopWPJ/Axp7A4VJIOc2pZt2SLVoImOryfcTFOSxeY6rYf9OUkM3vOxEMqByaOVjpvzdGKUoHlkX0yQ2Dcx2EdYdobvLW04GTJpD/IoAiWybZOsWrGV6WAty42befuSQ7Py/+YeHTPZVVdC8sxuXbjBiPyI/Msm3w09LwRc/xkyKqOSvEzI6lJa0g48kVV3euvqc751bYxn9JnAPiMgJSl57CN08rncmLig51KSupuS1WJaVi/TklqH5zU82/gZ3l3B8xSe3+F3bUJem+q2xx/gZccTWaXiHgs6OPOta2EvtM8YECX1fDKjTz422NoUEoY6JHa/MLfY5FNZ5Wtwx8pvtARL8nQBsmrPbU46q32wLTtYn3HJp9/r0yGrXHNbKIQ8wX67gLYhE666DLAA3+8TLWhs8pJ25FcH3K+FWtBjnRhbCYu4oMxHIm/t3ygk64ATR0Nw+3QXLQ55cOflgEHa+L6fTFzyQk4f9/MsFKquu4eTrmE9L21Ez4R5baUy6kqkidorVve3upL3GijE7Wv0vQ1RUjYtK3nWvXjz7pNoWV3JBxFHr3DqRi4JxOlwCH0iGjRsSdq6I6B0a4DwBQqjrMT/H3/QifarMEPydDtRrwM3de333eCwqJWZkkcxC3G1jdjkIRHBxMQB7sEgqzoWRU8IW7D+pMxr32MSFlmcRFn7ETzgY4GJLXiUnFr1U25n4mHGcKJDWla2cV6vpS+zGB7FrLaKC0zbxwp1sO9QeIL2KA6ShDxaI4jYg+sHi95Q4jxOLizJcSttyJ6/tyNI3ZWHR0pywnKO4aW3sDdDOj4ZRnqbkrWPj5IiKzw2noy8ver7UcINA/LoHaeBJztIjPC8gSof+YADYtvTJ+btbvV1I53RM15HyCBQjQjoMwivJH0R2w5yNMd22J56qCuJQs+hPGCxRhcYdvkPBnf2h5uD9zcAAAAASUVORK5CYII=',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD8AAAAyEAAAAACeOoCeAAAD4ElEQVR42u2X70sacRzHe9STGD5V6McfEPMkqvXACRVIQlEU9EAYKQWDgkYwajAQiSWLalQ2WRLpqjWxzGV6RFCsVVvUaBZhaVGstEYZhREkiTe+u8nd9/TOs3E5xnw/8nuf772+Pz6/LgVL6i/l9lNv7BeS/SNv1oXkzvG7g2aJZkqN4nplnZadee8IH+AZTREwoVbHtCycyzn++1znajQclyH90sMp/szbIaaDAxlNiZ1AQvhwrt7MBAea6eIM79TGg6vRF84AjyP8m7H4eDU6V8YJPsBjA1ejunFO8NtKdng1yt79EsB/rWWLZ58HE8CvtCcV79Kzxd/YOcCfednBeyc4Cry+JTb4aRlH+GVdfHir4zTIWdLtnYiHt4U4LDnHTqLKx045QT+nBXdbSb+AviWmkAvwLiRkgYXeot04dsZ2QbPkOnOu7MPbiSGjyWga+Nht7bbS9wZq1KlNAH+dSRxrONepNaSTq5xpZP8o6DeNsM0McfFXC96szW+LNbbQ0Pu+JXDgHWKXnmxx6dk/2qh06b1ZIM3sDsZ3S0Z8OPfkgUu/WGPh680vbXTdjEtPzWk39m3l6LvE0CT8heTzjoWvG291sJummTKNfOpwap3alfaZLgufuf2Ki2fTw3Ch//jk40EAJUPgk+QPPjGT/IX7b+DXSp6/JjTQgz/YOySPAvkEsGW0+svXSqiAkHVVoEttclRhUqRYIdyRIrKiOmFb9STvXPkLP8nL1hBSVOLTHsnIo9mazvtUy9iSNwRsEfS5Upda4KGznOTR4i0ZsOnjJyErO3y2RjX2uzVLK1Yw2dHi/Qi85lIPviPYssCjqAQqpeyvAgG2m805IuZl0uKfnpJH8sR7h/h+Yl8Thu30RONlRdFAKVLvBj4ibwBLo8EvrpH/C3cW1yIYOnxQRT6ttmpw8FR0qWc5jdR42YYXZtEYeHmDFCH/7y8nJsGWpZ5JHtDoSb2bGK12X/kwrL8chsuKwGjMwGNyqGdCsjGz64lFTQ57PnBRDFONwc+0D2njnumlES9mgwcXJW8YXgCWulT4SWMLS3yeGJ5ovkeHr8JWBUCzKLxXsFN7PnVhkXRG3L5PENP1WpfgiYTL0LkehlUgZP8GL6duA0SEsXY57YC/2TzzRTWWJ6bx/JC12g3f6QGfGe8TwDAwRnW+BOLeJ6CmHdxvYcsckRQBouY2PO6p2SOhpDuLwqb1bvZJd94QOZXRE7HoVngMe/kjOnTi4XNEdUK45gVVlozGllhlpwoDlikgw+MejGtrgyiUsEJW2BLWeqEfoa/rfmS9cBbF09S8YWsjqPor2o2fv4Q9eZFOhGAAAAAASUVORK5CYII=',
 		),
 		'mpass' => array(
			'machine_name' => 'Mpass',
 			'method_name' => 'mpass',
 			'parameters' => array(
				'pm' => 'MPASS',
 				'brand' => 'MPASS',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Refund',
 				5 => 'Moto',
 				6 => 'Recurring',
 				7 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'paysafecard' => array(
			'machine_name' => 'Paysafecard',
 			'method_name' => 'paysafecard',
 			'parameters' => array(
				'pm' => 'paysafecard',
 				'brand' => 'paysafecard',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Refund',
 				5 => 'Moto',
 				6 => 'Recurring',
 				7 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAVCAYAAACNDipWAAAEpklEQVR42u1ZIVAbQRRdgUBUVFQwSTpFVFRUIBDtTAUzSAQCUVGBQCAqEBUIBJ1BpMl1JgIRgYioqKhAIBCICGZKNnQmAoFApEM6g4hARCAQ6f9/b+/+3e7tXdK004TszB+Y7L/d2//+//v+PyFioy3mH/8S+WUU/F9Mx2QMAHO2IwpeR+TvQfqhFA4B7MLUQmM8+kLMAJh1H9Se//9RR+Ru/N+6DwrkUqMgyo33wpO7wjtbGsszfKzPkuCAKN3W0QqRPBeN6lwR565F/uRBgOvJFVGW/UAQ5LE8Bzimdk4A8BLkAgG16cLcGYLMwZ/YUW7WJxHgu2uRe5ukey2eLoLOrUtncgCWbR/YK0rVY5uJGMDImCEFV+BvLUH2fopnr9tibt6S62fAGJtwZ9UiUmpWwECvAr1PjTeGTqj7QVRaIWMvyW011/Ri0eUpfZjX4/OPJ7D/hnVd/J0O21i17y0P6N3xDDaAaQ14Njwnpu+q85xK9xHM7YF8sb4P7WN9nypJ8ftzw854znJji96Z277UXKZ3MNc6JoB9gtWNMme7QASvmoQEF2cpLSr39GIKtHWHXp+MYabJtjW6cD4wpOw41qz59+quc29P7ph7xFJ0qfnV8XwvIDRqjSMyLnecrAPthXvxUTybh7OcwLkXYnzhWyIJ1BEMxGopC7hKcgeJKS1JSs132QCWdwTYIACX5GKK02QDuNy4TAW4LG/dTtJYjRj+z4jeRgRMZPTlxpqF6e+lpmhIv+vZAc7XLQD3w5TWnAvTcQJJQa9GjySBVFSWh4Eu/jYIwHiAEKRiptJB7+2dv4TnWsHzxh6+c8TPiZGkSxCMfNs5ByFmaAMNhhYMCgwIvp62DQeQ6/x1gJMMwg9Ld6jsJkbBoABzR8IMQM+BYIrDOQ4sXgGYSpP2zgwwrO/JfeMcgwJMzkn38g4BxQWddewAjoIxGoAV8blKTpsQpcpAm6l7ZwbYkDMyNL+DMwEsTyPPuMAbC4D5HUzMU3urvBgaYDoIplpijW1f2F3ps3B+ByNjx33pnpM3QwLcpTNoBzLBSwc4TqT+e4CVRyrWiCWPG+DlaEmUCPBtwMJx7SAtNuvuNqODZGkip979cKgUnRR5gwAcX3/UAKONBwfYyqK5Z1/44ISli65ZkQXyCFB32am1pMIyI0re6tFU7AOMTFPfu4HwvSHK4k5EAJLeOfvtJgPA3Ygzx/flxnaBF+oUHXNr6QBT5tpyONk+VRnYvDC/INkFGyIpAJt1sGbWinX2HLrH0bLAta6NRVv1Fvza8kVKKVdJBRhLH3ctzcgkErqE9B3qbBkNEl3b414cUBvAlNFgH94g4swcg419bFgD8E5B2jYBcFv40QGbIo4U3WP3YJvSX7wIpyim6An1qCUIBuYHUORp1y9j+Jr30ToYDBSd19IiJ4kbFAlRRA/qX2SxOnNo8qOi3LN0kmo+Z7DsyaKJdP2uW1InSzVpqqqBwjprGNnokKYzmG1T1QCJ77GfqD9k79ZOskb/+SvbHTwdI2/Ojx5g1RaMRwnvJFWnhh9ngOOf7Mz7bmVq+H/6/ZRY5PYII7hismNLh2o6Usdvqzly4PW2s40AAAAASUVORK5CYII=',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAVEAAAAAD49anJAAAEQ0lEQVR42t2Y32sTWRTHW+j0P/Bpkbzpk3nbJeShLEgI3cUKWyRLRhYswpaC2IqNMoHBdAujGKaCSEw3DTsEQg1MAxoToRRCl27woa21oCWIFdyWUIO6EuzYMlx7enq99zZTrWCq23ugJGfOzbmf++N77rSJbLXayVltVqudJPu8NcGftSN/Bn5y+b1gEd+LiX0ObLv7437v8Rf9cfXnX//we08Uvg7yq6PFjlyxLDUuw3rLessG8K2HsK4vr+JaJw/6vRf9e487P9TdCZYrNi5HWSpLG8Cnv//9u7UjzH122u9F/L1surJnwMek4gPBfeyXOdGzFy0sd3eqba+ONjLHFvCsdvOH6DtmhvfRzcoIDbIjk5YRRMuYTy+D70kz9YCNn3p7F7z3XhtBswt7mV1G8N7rTfX3TAVY7FSAkLnD7HtqetKyIwzYCM4dhpzzQ2m3mJMQS7stJS32O4Sw30m70+6V9g8VxzNRTU3j6BekjEmjrh/aALbdJwqoz7z98xftnDFxq6H15GoeQko+3tfdmbTopgzLbL10BQZ5cY2PNIKE5Ipi74KL9qBbOiHzz3tb11sg4oZWlnBynFrNk9jKXc1fyz6/gJ+Hx3gR3FzhuXQ9rt87NMJvNd7u36kHPnPA0pyBn70RI52AL0nbgfseixGw6jD4j2/YqQBiFjtmYlT3b0t1W3rc5wTcH6dBkFBt+28cNjIvKnakmq/mV9pjFfBW887AZQmeZlP1BQJ6L60OLsJz2gOmg+a8loUiUnCxnM5yttIOGGD375R8GAejQUD0fDYwPxBMa3ad9/BrsBMwTtKZA7qiKwn5STPCJq3eVr63E7CujC7THDsBlyUjWHCVfGjZVMOAEWM3wHZEbePjllYJmbS293YCpnbFKPnwDNcDR0P4RMRrADCe4YwJ8zqgfgyYkKVVIxiWwzKeS1BxPMPjp0q+qUAovjPweU/GhAmirR6YytQXB46GQB3f3hWBFyQsRjxw32NQcTsCmxGB2cVRFC2QOUJQAZy3NL96zsA0frfACzsDM5XG2R5QdQULDFTXmRiugK5EQ6xY3dBQ4HQFtzEAP78ApxcMe2dMOkVhWVe0H+FTKL4dGM9uNET74rBFPGiiGM7EtgMvrU5U+YjR5Wdvmioj9D2Jt1lNBGZ1GPR6vUUUneuHsByIsUylmUHhqPSJPpgEEXh4TIzAtU1a/AaHNlGllxKo+MNjCMqA7UjSwksR6vmAuvny8Pe/517+ZjLraU4etN08cG8rnMKwHKvQMj4T0xX0qW0ZExPYkVxxcBG9PTkEfnoZv4MNLhY76ECvGOi7JBVcsDtAgMIyvafVPEZwQGU9cZ1qHri/8TctS0u7EzLe2LKpSh/9dXZBreZpj9Fl9Dd96v7Ji9Zum9MZ/qb+AfBlgBMfVgQVOe3e58D0BY/a/ND/EhhUEt97PtUyJlVVdqv69tp7fUWmd6agnD8AAAAASUVORK5CYII=',
 		),
 		'postfinanceefinance' => array(
			'machine_name' => 'PostFinanceEFinance',
 			'method_name' => 'PostFinance E-Finance',
 			'parameters' => array(
				'pm' => 'PostFinance e-finance',
 				'brand' => 'PostFinance e-finance',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Moto',
 				5 => 'Recurring',
 				6 => 'ExternalCheckout',
 			),
 			'supported_countries' => array(
				0 => 'CH',
 				1 => 'LI',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAiEAAAAADhip7cAAAF+0lEQVR42u1YfWhTVxQvs7C/BgsS9k+kC41VcbhoWLERakMXKjMSRqy49rVWu0QTaSdaKYqYbsvsVitVlwnJstauSRepMZnO0pplS8PeeJCNDKShC0akUjfc5nBjrIyWt/fL6fMl2i8YsnUmh+adc+69557fPR/3NQWJjU8SpdIFnPpJotFdecB5wHnAecB5wP8mHay2Hs6my7r/NeDP1zOWbKp/Jja6IOAIe6Uc9E82DmvJBlEsGFLsXNl0+vED7h9jLAduhJWgnjD4RaT0ni6lEVSc2jTSXsR6F7tZVNXdJM6udpMNIn+yoRDPkOJxAz7Vw1hOzoD84EfG8m5kEYDLC7KdbWEWt5XZUZxarxUlzbhkYU0tp+40lSSM048/wq0hxtJ3gPjj2xlLT3hBwFEVnGwoDETai9ZrEedYkEZiwSH93Fut0yuN1W7ir3pgY+fK7iaQj6fVDxdOrvzwuKidP8NY71WPJH3dsXuEsQwdI8nSx1gGkwsC7m6Cs+1F4Pc9SwnJqQMR43RxSmlcO9xQGFURqIZCimN5QUgRUlAsS3Wlul65k4PUUS/aDESgh2yylerMjtb42uHiVI3Dn3HHn6zww3ZJorLNbYZmwI75Tm7rcmSNvY52ZL32urJGhKCyDR0mpBB9sjXTcV25xlhefz+7fUnduvn8HICPXYWzA3YJcIT11JQkpBTVn82t0pIE6+2qksYjbNNpyQaovQiyj2e9sCPZ0owjfvY6aW1xCvnw1kvEi9rWODJPf1aaF1W5zWtqwa0dxve+Z7GP5wBjOb6d9vRpcvv14VVzAN65kqouFnRyMFnWGNYikppxJxeIbF0O871yf5LqOxYMaz01nNrWDDkQoe6+7S4kRKlUt2U/6hswY0EfD/06veOmp2bTCHjM9vED9lgwqnLchOZImlPX+cAZp3vlrXFw2+6Kh68/6+T8yU5TLFiqUxor20IK1ouGWJJYXCubBTDAlSS2LqfzK055aig+51IY7ainZO2V42my+WdqpLJNaazwizZoLRHqusIP58TIdVVhDvXtsJaSuquqo77TJALGYWjGkaaslzrKkJ72E3egwzlzHQdMPs13jaKV+TSzAibDIm0aAUyTDTGn5nHmulidW/bTgbzxK7IB6beni2wM2CkqLQzIU0Nt0NZMkUOkMcs4jVijvnNvBScXYaUkpd7QwpxL0Zh0J2SvoUKaC252K3sEMLWbOl8L017UKyeQlMY0TtWJ+5T1Om4irSBRvKnRiVkgOUdtEHFFy6EOwKlReyYb68VNUNnWaeqV0/0f1rrNUh5Q1N1miihlmegTGqhID7/URH7vCYuE+3jPvTm69MGjj6YHYoH6RDTgJpKTiM7dbSaIFBOx2qQrjNpgSEGRQ6TFyLXG+34WDyfCopjKGoUEjEs+UG+IsHQINQ6EAH/S/TE7dctzW9axL2YBnJalZaYrSqO6FpxEH30K46tW7ShQXcRzeKgntnnFrud2ZFLx5U/Ssr73KK1e8Rz6Ni2ruiY0LI20vnaMbPo+xJyeGHSunzJ9/NBgP54bkkd8Gy+B2x1My147KfkAW5tXpGXX06UajKtrq65tSKZllzZCeoGvHdvbvu3MO0+nZaMD2YBPvMlYDlY7vhRJfBnJApwo54XPi4KZvcv4nM+U9e2nxEqpmOAMPC/JGyZYA2YwEySfck3yKoG3uaT1ZYLcOMHzpwcx43ZGRxbuCysbZ1YyE6uF7+7BbB+mrNAdzvBxa8XMTNji+e7B1Q/qFz7c7s8G3HyesfSPzfvvYeoEzHAGznDLyj/y+X7fBZfTFTZMZqT7wvYhQ8AQt04+mJEwhIS1fwga2LidtVa0eSPDkS4pcIkZ/pb1gitunbJiFEcg+ZBra1LYI2AIG+7N+PeD9TOD0/WxK2aYEjRjp7Lez0aRxtFl8wK+8ye/pD/ffCfBGhjGO9YCPwD85ljKcCeFS+ero60hoqYoY7H/tQDg6ZalDPiXk5z6si67L7teXQBw8vmlTIly3L0X70g0WwXnf8TLA84DzgPOA84D/o8B/htWY2abPy2DpgAAAABJRU5ErkJggg==',
 		),
 		'directdebits' => array(
			'machine_name' => 'DirectDebits',
 			'method_name' => 'Direct Debits',
 			'parameters' => array(
			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAAyCAYAAAAqRkmtAAAEFElEQVR42u2ZWUwTQRjHG+MFKjHqgw888ACKCQ8aE+ObPICaaDxQ0BgSiEc8ovHCIx5QS1FEPJDGqAQKAkEBpWqiRSVFeglFa2y0WoOK1KCpwbMiAf2738ASKkV3G2kr6Zf8s7szszO/nfl2vt0ZiSRgg2lSTagkTZs+Rm64HSzXG32hsRkGXVC6TiFJ080eCDJshEz3ddb5h5/PmlpRaH7nMyVefoaRMn2HJLVuWT/O4HRD0dKLTzrhJ0adxcHa+4FOzDQ+UFnf+wsnvnR0gXNDcCM9yQV00tF7Zs3Lj25vsrxziho65YUbOLdDCqMsEygsFC6t1qXdHtCwv4IaWz4jpsjSfYNAJUyfg6jwCEyLmALFhIlcaxLhSk4WD9re+QOhxxtEQc6PXc0AeXkF1Nz6VRQkadXU6d4HpXOxoIvDIwOgHoNOPlYPqaYZ448YhYEOHw6kpABLlngPlAtzsDq+sXJhJ03CQAmSjKYhb4ESHG+CQQkwAPq/g1IgePmhnalvUBAEWlnJPV2Ye/F+7NPpiQcVYQHQAOiQB6WwSeHzd+VFzWBwvOqDgvuDms2AVOpeKtXgzaMuFh098Bzp6wl/ZcVTRCsfMe2YGoWk0NBeVYWEBCLT0AIdna6HtvmTONANG7wPyn+THqh5Je7DOTERWLgw8Cvi36Ce/C7HRUR5H7Tzx0+XF0WIYueu8z4omenNF9GrJXEz53p3SadvzxIw5QlVTY0JpYdzYL5Qxjm7RrisVs9BfWlDF3Tfnr1u0/POnR+wkWq1Gg/pk06A1d+rR9WVK56B0rJjtt7eC2p7ZkPN7TvsWnE6FzabDSvjE1BZXgFTQwM78nnU6P3GRjQ1NbHrrq6u7koVCqClBW9bWxmc0+lkZemc2rDb7eJB6QVae/U5Kh87WCVZmUeRc/IUzPcfYN2aNSgtLsbObduRxIXF41lZ7NxisWDRggUsrYTLr7hUzspeKivrrjQmBpDLcf3qNWzeuAnKggJIU9OgvnGT3S8/JPMsMkXmNiKl+gUDJRGAMr+gd5gobTf3L069R2n0IFSG0gvy81GkLHId0p7ph8rPi4llrhG/NA46rY6V+93FBIF+aO+E/O5rZGpbWAXkc2fPnEGtphZbt2zBkYwMdqQ8GtrSkhIc3LffBfSW+lZvWWb0BbV8Oes5chtVlYr1JP/wHoGyRQ1u2CmN9z/qsba2NjaU1Bu13NzH59GDaOu07AWjNMojH6Wy5BLdw6RhKyWUdyI7Gw6Hg0Gbubqol/m6/ghK2zfq521+MzXRy9wDOtkFdJhUVxJfbnX6CyhtAwXLDe/dbDEaIkfK9M4VFU9/Us+KCZn/WruqX3QEyfXfJam69QPshxoix2UYi8lffalRMr1Kklo3L7CLPpj2CzVm4QqPFGMTAAAAAElFTkSuQmCC',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAAyEAAAAABfvcoyAAADDklEQVR42mP4TwPAQEND301clzohsuM2+bB7xbJzd5WRDH0jkm3XqbZ/45G1lMBZaRkzzwjCDZ3vP7WBGt7ev7E0Dm5ox4dzXdQw9EdLosbnHqihnbo3zBBST9Zh994StvqD01/sVcEGrx6D6E3UeCOCYehd4+6CRA1s0NZfbYVaqaWpTzc2OEEKp6G/mYvmYjfSs1atFATJMPSRAXYjEzXMyTf0hhkuQw0bB7ehBXIbdmUrYRrqHz1Pt+U6WYZmLH6u+/9/qTSmofOA4ntVyDK0VBokhs3QvSojwNCiuW943vBAsgOmoUenJQsgICiUKUxSIEOxg1FDB8DQbKUNu2DQT8DSFASdTsAMvWexbD4CnkgnKZ1CQOVU5FRJceKf/rzjY8dHOxsDCxC0Sxwx2TRN9dZW7IZO3U22oaASdZ0N9kK617OJfdjVUbiraOMSsg392wiJFiyNiVayDf3///4mXG0Uu7VkNnsgrr2/6YYZNrg/fKLYhu2XvmKDj2/iNZQyQF9DK6oR7FkByFp2sl6QwTTopNG633gM/c28wxRk6C3nPUX//0/2vHUrLHJ14mnx1Ykg3rrfZyXv3p3s+efP//9bNr3xfmFy0ujr13W/TxpVVD95gtPQ+5vmJ5/hrqjutJ/gdq4l+e8S28KSWKfuwsKSy5d9DGOdFn9YlZD8d4Xj//81aSsLNmVk5c57XW+6PbewpKkVT46q/L3SvqK6ojrWaV4IyFMV1aV8ZyXX/Z7gFutUUT331YJiiFdBSeispKvHBZmghUd01/2GBRkWQ7+xbbbcur2ieifrdIMDmnnsbfJ57BXVf/4s+VhdBzF0Zy9I9P//qbvbVzS1hkVukCwsAVmPx9D//89w3zADheAEt3fvVjhekDnAA+LtZD2sMytgdeIBnrt3Vzhevvz//6WvR6cd4OlVe/26qfX8+bOSIFVohnZ8uNxGjQT1mzlR4yM/1NBZa2cVUcPQI2uLLsA7Z88tszlmMF9uw549iYWrp2Rb71+K1OF9bjm/qVOXMjjV8PJk2nfNqQsAzAyRrevQ0QEAAAAASUVORK5CYII=',
 		),
 		'pingping' => array(
			'machine_name' => 'PingPing',
 			'method_name' => 'PingPing',
 			'parameters' => array(
				'pm' => 'PingPing',
 				'brand' => 'PingPing',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Moto',
 				5 => 'Recurring',
 				6 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEoAAAAyCAYAAAD7oU3dAAAE7ElEQVR42u2b/YtUVRjHbyREv+QfUP1cvwTRbxKI6O7sWgRBVAhCKIIQFEEURRAEQRBhsDW7d1N7kRVxUURRk6jE2LSkF7fVnTszu5v75u66L42ujqPr7nE+R57x7Jl750V224E5Cw977z1vz/mc53yfM8Ncz4v4W3dSrWre3f1Es59eUw+2vr33mVi87/E1O04/7FXyF2sLtsb8YLixLVB1a61BT8xPrQ0F9HKnerDBTx6ta0Cm+cFCYzyxqQhUvrDLAVpsDW15WF8Ez97bbq2J9x2YSLtiRtOUAxJtMT/5qrexJf2Qg1HOkse99TvOPepAlLVRb2174ikHopyoJzMehy0Hw4FyoBwoB8qBcqCcLSGo575K1sRkXtydql1Qbx0aVJdn55bVyUqs5ZdxFUzcqF1QTX6gPjg2vOLR9MLOlHr9wEWnUTWvUa9826c6/phSXQOz6kQio+JdE0WaZEfUa3v71b6/ptSR8/+pj06MFtXnfueZy7rPs4PXVOff02pzR39JR4mWQz0z6szFWf3/w+9HiiIKGbBlAZ+P9WbUGweLo40xD3bPaB/wBZ+i9LYkKDrPZG+rBaXUhfGsdjI3t6BGMjcLE6PO3PyCBsr9Jz9e0vXRiwN5J7K35tXgzE29RSl/6Zu0vpc+f05f0e2pt73z30j9oQ6GDz2Xsro919IvsAemc4U2AJL6GNcsnJS/c2RIj8lzfJA+6SMMViQoHKBRejKntu2/N4Et+wb0AOdGruv7tw8P6QEE3PT1OT2o6RDln58a1/c4NZu7vahPIJMQzHZmdLI4TBzI8vzTn+4uyHdnJwtgxq7eKvhIGTtB6gOSZ/QHCOoyB1lgcy5mu7KgCFsa2eGMEcoCxwbF9de/Ty7aZoBlK3LPpMMc4RltTYAYfdE+bJWZLNFtg0IK6MsUd5kP/prXdp9EOYtdMSjZQuYq2uRxKAwUq21PiIlIuUSXrUFhztPO3FJhcIl+E5REm6l7XItvUk7k2X2yMJRVDEo6C5sUAk0ZmlItKCIK8YxamDBQsmXsNog6GmpHVDlQElH+rxNFfcp4VYNCo0QwRU94hqbwvFpQZBcE1BRuVpY6pUCRnewIZEtKv9WAIkPiP5FqHpTJpPh2X6BwADDQlwxFVMiEqgW1aU+f1hX6ABqCSptTfVcLoABnijT1qUd9/EBHqMtkRRrQzUpBcf/e0WGdVNAj5sUYzI0x7guUZB4cJq3Tqbm3iSqZlOiGmUkkvZsRhDB/dnJMp2uMlRSNop6cf0R7zAz75/A1ffQgi5nRgI8CgeeU21mcZ0STPGPRODsxBhHL2NzLdq4a1FJbWAbDQVbY/sxoglpuHzAWAltxUEQP4c4WIoo4Z7Ft0BvzWLHcoIhkJOXjH0b1DiGZkGSYc9gp/n8HxUqyPYFF/xihTkSZSWO5QaFZcvgVP5AW+6NRWVAc/MJCcKm/P7L1LOzjS1ikLaWhVeW+Jir5WS9shVfCasEP9zWLA+VAOVAOlAPlQDkLA+V+SFYhqHUt3Y85GGV/oD/keUo9kL+Zd0BK/t789N2fT/uJ8w5ItG1oS72rQTW19j7vgERabvErHq3BLgelyOab4heaw96HOe7gFOxG45fJDaVeQ3szL15j9Zvhktn8/8MbO357pKL39nj1Q7/VEE89XQ8vNsba/3myob1/dRSPO7cnG7a8G5FFAAAAAElFTkSuQmCC',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEoAAAAyEAAAAACOWs5CAAAEL0lEQVR42u2ZUWgjRRjH8yCcUF9E8KmyHHkoBJ9FWojog5RiEYU+XchDkK23EPChQvQeyr7sXmxiU5vQcFtDDXdgXELuHEpESmilraEmpvuQyBJDe7Y9l9AkJaRUk8uN/TrObZo2R2vLdZHsQJjd+fLNb775fzPLjgm3XE/40v5O//MvmqXKND7QOUy0ojj8XwrcVZY7f/4x3wL15NfvPr1aIFLEz7O9T6HCE0ZAOiozWx8eQa2+Zhikw+J99Qhq8jMjQQlcLm96HDIWksBF3jNV7UaD+ipjKt4wGpT3I9NOfxeqC9WF6kKdGcqduoRubl0qVHi8ajmfy5PlB/To5qVCiVLEd9E4TYyG1v9/mpoaXB5TXQr74w7VEo3UTM/q7XQ8aqPP3amEX3UV+pLmgPe409B6qjfvSfXKSzRS4XEqBoXNaHPD1DLgXRsp9KmuhL9VuW1Qc8P7Kkbbmbyn4SzbobO54SY/NShwD8L4UBlrL9aZ3SFRErjJ6O4QWGblJl9nZkOtCmryTT7v2bqGUd4DtklzMQYtCgvP854mn47D/b2HdabJZ2WwLMZ0rGNQolSMaZJ0HerBSp3ZzAnc3fsYAVzNunWNOMIo/obAZeWDErGcGqxaSBuJZ8OpsJNRqCMGo5/eAZg9H3jEaHkMnifNGM30uFN7vs0cDJj0QtpOQIXHMaKBFriMBjgUCqPFFTJpdWb1tsA1nLqT5TGMCKDALa7UGX3Me76ynUJFfBgRwUM/d++TX2q5nalZT4WCKSJjpPQRnw6FGNqRwsI9xItqSHevsGSqdFxRIlAQN6K+gBe8wX2wQi0XVzA6FQrM9K6iNoxmQ52gGs5CX+tgdCiYGtqS6t1XaaTaoSBSCw5qCf/rCKVJIE1QiiZVLaLUCUp1NXki72Blz3ccam2ERrDOgO3pUBOjVUsxRhZmeanJPwNKYTVpwQE51XBCR52gpm1le8OpujZzGP32LkAFK0TUZftmTnUtOLYzGFUtIIeMdhqUwH37ykGpZs3KZXuTV13PgIL8UdjdoaxM5luUoCtQB8kTSHkSIXdq/oV0PB2Xl0BTsyFYg0A/JGc3Bh7dTJpJHGZ6AMF7K2mmWZ40T4xCbdqW8Cvs2shsKOGHie4Iddai51jCf1Ci+yOBOq8HgdsY2Bi4MJS8VLMuOELr9x5mtDpDlovzQqXjmhT7JFh5EC70YaSv8/8Zyp1aHqtZMcJoX034SXKcFyrghSUYfOwO0S3pBJR0XQ/hWd+TqNL0TUaP2tnKtK391aht79NHfIHPOVL3Hb0L1YXqQl0ulCE/mlVfNhrUdNyEvxcN9tXzmxsmjKX3jQX1s+8Q6vfXjYTkXvz3GGT+baMgifbCx09PsSJvGgHpi/6Nr48drf0SnQpcKdBb8p2/59vO++B6HKratfLzP4QsvfRXuZXjHwuqmeFGPCoiAAAAAElFTkSuQmCC',
 		),
 		'tunz' => array(
			'machine_name' => 'Tunz',
 			'method_name' => 'TUNZ',
 			'parameters' => array(
				'pm' => 'TUNZ',
 				'brand' => 'TUNZ',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Moto',
 				5 => 'Recurring',
 				6 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'paypal' => array(
			'machine_name' => 'PayPal',
 			'method_name' => 'PayPal',
 			'parameters' => array(
				'pm' => 'PAYPAL',
 				'brand' => 'PAYPAL',
 			),
 			'not_supported_features' => array(
				0 => 'AliasManager',
 				1 => 'HiddenAuthorization',
 				2 => 'AjaxAuthorization',
 				3 => 'ServerAuthorization',
 				4 => 'Moto',
 				5 => 'ExternalCheckout',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'directebanking' => array(
			'machine_name' => 'DirectEBanking',
 			'method_name' => 'Direct E-Banking',
 			'parameters' => array(
				'pm' => 'DirectEBanking',
 				'brand' => 'DirectEBanking',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 				4 => 'Moto',
 				5 => 'Recurring',
 				6 => 'Capturing',
 				7 => 'ExternalCheckout',
 			),
 			'supported_countries' => array(
				0 => 'AT',
 				1 => 'BE',
 				2 => 'CH',
 				3 => 'DE',
 				4 => 'FR',
 				5 => 'GB',
 				6 => 'IT',
 				7 => 'NL',
 			),
 			'supported_currencies' => array(
				0 => 'EUR',
 				1 => 'CHF',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,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',
 		),
 		'masterpass' => array(
			'machine_name' => 'MasterPass',
 			'method_name' => 'MasterPass',
 			'parameters' => array(
				'pm' => 'MasterPass',
 				'brand' => 'MasterPass',
 			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'ServerAuthorization',
 				3 => 'AliasManager',
 			),
 			'image_color' => 'data:image/png;base64,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',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE8AAAAyEAAAAABocwUGAAAEt0lEQVR42u2Yb0gbZxjArxo9yrHJvGhwcT0h2y5b4g4NW4uZXA1WsptGltL7kLFYbiPCwQSrZuwM2cy6dEvdfahFpl2PWqvQfPCDAxEGx6g0gzCyEiGMbIatjDCksFWoA8Uw34Wi6UzyeqfVjd4Dd3A89/K753nf5x9Sd4iv/n7kMF9nz/4f8dD5p1uqcB1T2XX0z0J6mgH9hDFT32fMaC2agX3HK5NqOxs22hbs61vSerNho7az5E6uptbS+fvQ+UluamFLRMY1/uLpfcIrk4zTdu12sFzI5zGNJqt5bKyX2o6VKyLT+P2e49VNtN7Mh/ZQWn7SMZqBropcm+0kQ+f1E3uGV3KHul0MLSvtLV6qGFpWJBbGhhB4ZVLT+3Bwb9V9cM+3+Pn9KTMcon1QPd7Yaw44OMd9AAfkCwEOb2qhqUkl3sspOLg3Ur0Xs3BALv8A6+K6NRV4VTgcnH393eEtON+i/7tr1XCAIlOiVYo39noQ1rGD5u14/+xASAe33lOIp2+HtV33z7lwQK5+A4c3euXokCI82BP75uSjtgMy/Bus/egRBXjoPKzt3u7+N5xv8aMHsAGml1KA99wnsHj8szvh+Ra/OgmHd40vT+war74KFq/v6s54IzKse/MVCgXwYMOxfX3wj53x4MPziRd2jdccgoPr+HVnON/iRQIWL196K4BH34CNefnwLtTA4jl/2TfrtX99INY75HvvcZ5cY2bXeLWdauPeFd8+xj3VWeMZ2KzRTyrKuSc+hc25gumx59xdVSxG5RXLlxsKKxZkzHpaab332WW1QQWiWtaaYe3HPcipljvgq+VC04OivcZLfynqNc7AnlnDCoaxrIpO7dVuSAffftipDXfAOra5UXWfq9HAnuDd9rkdF/ZkSnBk+RU7ZIjBB+5OeeFayHyJbN9mLCd/rH6vPMFJxeGCS4X7WwUTKo2G/DZ3dJZvQmVY+fDjQme12GxAxXyvZqih+tTSI/O96pqhI8u5mjX0mZbgUi7YyOQ7p0yVhdruPZqOlsefcmnNWnNlFzpfUC9xbKy+r7nRVKmfwO4+mS3/B/Gmp/X6A8LjOPfmhaJuN89jGIIQhN9P0z09CEKSgYDXazA4HKmUKLrd4I3fb7MBHZYVBJJ0u61Wv99iAetQFPgOaFkswaDHo9OhqMcTDAJ9FXiZzKVLicTKSiiUSPT0YFgy6XIlEpnN4ntuThBYlqJIMhYDTxxfXT13Lp12OGg6k/F4MEyWZ2Z4fm3NYqEooLO8LMsIkkqBHyYIjpPltrb8+RYKLxoFfw7uDCNJLDs6ulkH6ldXga1isUAA6MgyQSCI251Oy3IyKUk0HQ5n3x8/jiCCEAoFAjyPIDYbwLPZkkmO26zG0XB4dlanU4UHFiQIcKdpSSKItTWr1WYD1jOZUFSSBAHsPZerooIkUymSNBgwDGhmv41EcFySnE6GSadJkufBOhYLsKvTSRA47nDEYqrwRBFBdDpwpyivF+zFSEQUZ2ZKS8fH4/G5OYoC9ohGx8cRhOdv3bp+3WTKagK8UCgaDQRQtLRUEKLRYFAUcXx2Nh4Ph3U6lo3HIxGOwzCGOZDAknX6oY17bW3grD8JyweH9zdUMvO8oj9pPQAAAABJRU5ErkJggg==',
 		),
 		'openinvoice' => array(
			'machine_name' => 'OpenInvoice',
 			'method_name' => 'Open Invoice',
 			'parameters' => array(
			),
 			'not_supported_features' => array(
				0 => 'HiddenAuthorization',
 				1 => 'AjaxAuthorization',
 				2 => 'AliasManager',
 				3 => 'ServerAuthorization',
 				4 => 'Recurring',
 				5 => 'ExternalCheckout',
 			),
 			'supported_countries' => array(
				0 => 'AT',
 				1 => 'LI',
 				2 => 'CH',
 				3 => 'DE',
 				4 => 'FI',
 				5 => 'NL',
 				6 => 'NO',
 				7 => 'SE',
 			),
 			'supported_currencies' => array(
				0 => 'EUR',
 				1 => 'CHF',
 			),
 			'image_color' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAABYElEQVR42u2ZLW/CUBiFKycRCAQCgUA2/UgnEPUTkwjEJAKBnERMTkzwAyonEfwABGKCHzCJQCCRiAl2TlLREEJp1+62zXmTk9vb9H486blvb3MtS6FQGIsgCLqu675Be8/ztiifagdh23aLANAJWkEH6Mf3/U6tQBzHGWPiZ5Qj1glAENTnRieGSewy6kgQaMj2YRg+xPdOWfsC/GORIOec+sb6mKBc5u0D7cMqgPxZpYHQ9xSuXyj6HmUEbRKWyvX22A/6m8bJoVwQZKReykLvY/DnOPXSUl8XvueEV3hugXLGtMwsd2VNmgUpMLkIRCACEYhAGg6CXa3NgdKEdoNKg2CQD9xb36FXWeufrNVhvQgZBcHG7zPHT9dVyVoCSYDEGSnrD9Rab0QgxWWt90Z8R9CuLWtVbK91U7XZ/aZJ1hKIQAQikFQQfjt4lFCmkscTOuhpOkhkUANLoVAoGL8gCnE5V68QmgAAAABJRU5ErkJggg==',
 			'image_grey' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyEAAAAABrxAsuAAABJUlEQVR42mP4TwfAMGoJDS15+XnaSc/1sYcOH6aZJZ++eq63nF9g41ZplvnmBY0s2dZrbLzzxP//b16YZc6sp9ASn2fYoQOPsfH58/////ztwGM5H5eqS2eIssQYDwgOXruuuBifijM3KbaEMCDJkp0ndp7YDAQz6xt8kpNBQYXPfw0+Kxe7VZJsydPXqKKPPh64M+1kcXGCDiTsg4MLbDqjl908fPjTV1hcUmwJYTBqyaglI9WSm5fO3ESH9+9T2ZLebWkz0eGCnKEYXG9ePH1NGFJoSaU5rsoJGY4mYaglaTNxVVRpM0dzPFGpa0IMHfLJ+/dDMbiwlV3okCalMDocTcKjlgxKS3q3zawnDUK6F4OvE0RjSxp8KIGYrbLRsZURagkAp+ib3uw6gLQAAAAASUVORK5CYII=',
 		),
 	);

	private $globalConfiguration = null;
	
	/**
	 * 
	 * @var Customweb_DependencyInjection_IContainer
	 */
	private $container = null;
	
	public function __construct(Customweb_Payment_Authorization_IPaymentMethod $paymentMethod, Customweb_PayEngine_Configuration $config) {
		parent::__construct($paymentMethod);
		$this->globalConfiguration = $config;
	}
	
	/**
	 *         		  	 			   		
	 * @return Customweb_PayEngine_Configuration
	 */
	protected function getGlobalConfiguration() {
		return $this->globalConfiguration;
	}
	
	protected function getPaymentInformationMap() {
		return self::$paymentMapping;
	}
	
	/**
	 * This method returns a list of form elements. This form elements are used to generate the user input. 
	 * Sub classes may override this method to provide their own form fields.
	 * 
	 * @return array List of form elements
	 */
	public function getFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $authorizationMethod, $isMoto, $customerPaymentContext) {
		return array();
	}
	
	/**
	 * This method returns the parameters to add for processing an authorization request for this payment method. Sub classes
	 * may override this method. But they should call the parent and merge in their own parameters.
	 *
	 * @param Customweb_PayEngine_Authorization_Transaction $transaction
	 * @param array $formData
	 * @return array
	 */
	public function getAuthorizationParameters(Customweb_PayEngine_Authorization_Transaction $transaction, array $formData, $authorizationMethod) {
		$parameters = $this->getPaymentMethodBrandAndMethod($transaction);
		
		return $parameters;
	}
	
	public function getAliasGatewayAuthorizationParameters(Customweb_PayEngine_Authorization_Transaction $transaction, array $formData, $authorizationMethod) {
		$parameters = $this->getPaymentMethodBrandAndMethod($transaction);
		return $parameters;
	}
	
	/**
	 * This method returns a map which contains the payment method and brand for the given payment method and transaction.
	 * The map has the following shape:
	 * array (
	 *    'pm' => 'Payment Method Name',
	 *    'brand' => 'Brand of the Payment Method',
	 * )
	 *
	 * @param Customweb_PayEngine_Authorization_Transaction $transaction
	 * @return array Brand and Payment Method
	 */
	public function getPaymentMethodBrandAndMethod(Customweb_PayEngine_Authorization_Transaction $transaction) {
		$params = $this->getPaymentMethodParameters();
		return array(
				'pm' => $params['pm'],
				'brand' => $params['brand'],
		);
	}
	
	public function getAliasCreationParameters(Customweb_PayEngine_Authorization_Transaction $transaction){
		$parameters = array();
		$parameters['ALIASOPERATION'] = 'BYPSP';
		$parameters['ALIAS'] = '';
		return $parameters;
	}
	
	public function processAuthorization(Customweb_PayEngine_AbstractAdapter $adapter, Customweb_PayEngine_Authorization_Transaction $transaction, $parameters){
		$response = $adapter->processAuthorization($transaction, $parameters);
		return $response;
	}
	
	public function getAliasCreationErrorMessage($parameters){
		return Customweb_I18n_Translation::__("The payment was declined.");
	}
	
	/**
	 * @Inject
	 */
	public function setContainer(Customweb_DependencyInjection_IContainer $container){
		$this->container = $container;
	}
	
	public function getContainer() {
		return $this->container;
	}
}