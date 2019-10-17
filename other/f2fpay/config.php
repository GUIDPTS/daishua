<?php
$config = array (
	//签名方式,默认为RSA2(RSA2048)
	'sign_type' => "RSA2",

	//支付宝公钥
	'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsyaIIP6Zf9ZyEc5Fp4xmGtkJDFxJFbvMqS/GXmvdL8OZSCdxvM04cU1Izgyj6hrTWX4Qrdggp8v0y2NNvsGOr4mBeMQc2LsG8ZSRaOEM6IQSyski0YM8EcSpdmg+G62p6fIJcr/MZ2WZxlKGw5/IcRO1hOHCxPq0KnTOxfbRGdMcFHukm26/fAD5gAOOj/sqKv5H8AnRkK3HjWPcNQ7m3rwDeDGq/FGIemsgXrnHqrzbvlyW6TqTfGZOFK0LTsLeNIZl9g5vbhcINMFy6s+0BSmmjTuzsc9iu4v54Lqaqc2N/cPHZSJCzFdUTk2A2zUYITULKP5hZEjMnZNgs5EyuQIDAQAB",

	//商户私钥
	'merchant_private_key' => "MIIEpAIBAAKCAQEAsyaIIP6Zf9ZyEc5Fp4xmGtkJDFxJFbvMqS/GXmvdL8OZSCdxvM04cU1Izgyj6hrTWX4Qrdggp8v0y2NNvsGOr4mBeMQc2LsG8ZSRaOEM6IQSyski0YM8EcSpdmg+G62p6fIJcr/MZ2WZxlKGw5/IcRO1hOHCxPq0KnTOxfbRGdMcFHukm26/fAD5gAOOj/sqKv5H8AnRkK3HjWPcNQ7m3rwDeDGq/FGIemsgXrnHqrzbvlyW6TqTfGZOFK0LTsLeNIZl9g5vbhcINMFy6s+0BSmmjTuzsc9iu4v54Lqaqc2N/cPHZSJCzFdUTk2A2zUYITULKP5hZEjMnZNgs5EyuQIDAQABAoIBAQCYJT8zFkZypULXEGFje/8zCeN/VdjT1lxnyyUjB2QfrnQ9LRKRD5DB2KreIyoKJaIcvM2ZdpW1K6fIG5vpRyTQu2zzjUoaXiv1ewFLGuYFijUTd2JUoKmhRW3OG5Wzl/dXsbCIfY8wuL6yCWBePxLRxbVBFyJ8e5YLAIyi34yR6qaCgfjFFWKtU+yYU/CtC2KJ89Ko0KLRrM+hujLzeg87Dda0FsYOrD9guhesTupS56J8FkRJnZSaIvhbfNaAdxM85y/1qVY710OX+njBb7ReFbO8mfKdO3zdkkEcQZFbs2dTTsR/o9rNzUkO9K3wxhJa7s7NmeEz4LFidoBxvETpAoGBANkpM74tkdQZdeGmlo1OPWwOgXoPqCX7Nf7TOKjaf6TkML2XV7P6wdEsX21R83ha4rMUx+nbndTOUz7/A+f43gBcs7m4iSqomXd6Shygyg/2eR8ZwyKQzpXS2pNPIcQQqXuVHGOzCfnhfFeqaDPWHruzpyWflbXEYjMubAUTtsizAoGBANMxAeFfwDzsAs9N90e8PDs2R6rx30Qxx5bDyFOD288RglUmKMzI+gnMHvXZ+ZBeS3wNj8fG5ASaaKwODC8hw9fo51IYVQUp9Jp99rss56vZlUz/vUTYVucoe68xWoQ+Go4ttxfUUEu0z4e1zFlUqLGyykMNYmI+uP5xiWy3E9TjAoGANt2wEwaUZ5AfNmnOc9kmr5xiniJCi4YSuFVJZW/++/wjTGNmZzSdr2mUmpwv9WKREEnZ0H4KPG/8nFf19q/r9w904SF8lmiOjZ86Bk2hf7L4GyF5KcyIRVZGnx1JHK5RA8ZCq/XaOWX88nE9botUrHvRaTIOsyPw45mA9S9r6P0CgYBAmq1IlFHqUQhLAj6y4xJJysEBuhvicJftttaBNcT4AxMrmRDCFHjopliSPKvrTe0pdbyWEHGQNuvuNh3kDSAr3ENnCap4ee0plX70ozf7igPyGgl8FfH/pJKxdReLfLQC6lkoIPqvLhCOIz7v1MYGFftpp56BD07oBzNS3m5ioQKBgQC0HYELRUYLyfN7r/KP9Q/9SJScEmEUuruAz1B8llnQe91ZSLlBcmQjjM2qRHqSH+T4DMPRW4GuMgxDoiFJS99mXxX7HFrPi+w768Sz9nOAIMj3koePmST6QVe1M5JlI+cO/vgCdkx/rR3awH2iz7TW7lFfY8WmsKjk7ujaUjp0JQ==",

	//编码格式
	'charset' => "UTF-8",

	//支付宝网关
	'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

	//应用ID
	'app_id' => "2019100768113343",

	//异步通知地址,只有扫码支付预下单可用
	'notify_url' => $siteurl.'f2fpay_notify.php',

	//最大查询重试次数
	'MaxQueryRetry' => "10",

	//查询间隔
	'QueryDuration' => "3"
);