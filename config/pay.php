<?php

return [
  'alipay'  => [
    'app_id'          => '2016072300103744',
    'ali_public_key'  => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApV1i1Zh0EhCvDR2DAe1tYnr7VjFpkv1qSKIbdfIla1A3Xp7kjXP0LwkE/sSSoJSDYmen2rHyOg7JHX5QsZOBEBw0FpepdI7/S42QiVJFuHuwus6wAgHemB0Rb1Wxkl8PKVXFuuGbbmJFexzWzEsJ7xp4KEZZDaDXHQKESNNS4tvTLIBgcnN1UmQ8h9LJjRkavIftK0xvcDNl/J9J+AgndPJRpYr2Ty+jkAjUBbNWt2jJvaxXFvhw5FzqosJ9jctD1ubY/UPPpFoq0doi+gEF0s7ixWVXbgJD4gasfDo8igw4g/X5EtxoIbNV2OtAu5nTrl/D93pKJk6CUwLx8DhAvQIDAQAB',
    'private_key'     => 'MIIEpQIBAAKCAQEAtQFedM1A48G7kn0rXmbxCtB9O4Jjo6IYyYjQbSdEpQtEpNqlm2oKSUuuQAm6+hW0q8cTcBAj4zVb8jLbCKk97b0Ad9r5E5q/Oj6Wz4lB3qKJdOdI1xnMG4u1Ih+tsRB3sklcldh4CDhO2MP8kt658NEahrfudAUnwByNx/iZt2rn4llwUfLEpCZwXOVv/TTheSU5fwGA0Ut/hoJsHqG3JkAZvjjXTZ5kalNQ+db6ICxUW+E3xlwMYk/RvTRpOUhQIOSWJJ8r48ClZZFYP1ZR98DoFVz33EA/b+Xrdnbomm4NyEatnUJj6uot1EJuAOsAW+7ByTqmcf8Nme0BSILBbQIDAQABAoIBAFRg0f6THFfQHTe5v/je9ij7bhXKv9YtH277p2Xq81YY/aP4BVGAylDGxfE2WAJzEekuAYKxE3m8iyNJz8+mzabA/7Hf01LvRYvKV5Vhv+IgVQG5O7yCWyLQKt1AdUDgk1v6VP3JjpMOZLsqCdkRmQ6I+9unbLKLIK+u3+1Dl6znkOgTcwa7K79720HSk6ZifglNDl6YdMkIuKMRcc5QGqk5h33zvi6tXBsnCFvFmFg6BcXXrA2iNCin4jQP6DjZ1V+c+FmXoYon/3nr00ynMHTFxG+UmkuXiD4Cfvx39C4EakvrT7zd5o96R6FIAKhjfEYyup1RcYMFT60Ra1rLnaUCgYEA6MFHFgSHU8olz/OLnKQgm7OCnJ678KhqSCzwWgSG2YramzBEpeh6IUFBzy7zdXXUalo+xSEJRFSza/HqmX69+/dj4pnhWRESontrHFVw8DX1OXXV+w0tL77T9c1xNsU809x+CxwxoW/082O83g7LWAOjod26TnM8zUBMqSgqUqcCgYEAxxUJLSkxmhs9WJxiDNs4DJeuf2IlX+G1gOO4ueCBZ6wR500+9iP+0yo/rGMGrWY5VERffRnLmQu1XHOUc/nWjffTm5SYitZJaThErLarIIxxIdqrQ1ywM8CQ39+/siJ6q1nAEumpGkBtzrEuNQ0DFij1QPwZuwtlflzzwgg88csCgYEAk8ZKpK8BYHBiq3G8PRpzXYeOMHQTbMrwl5x4iR6Ao1OmUYtGunMsPzrYVns/tV63G0LqwWulH44LUEiKoyO16Wh7MV5zlYHia+ih655PkyMe63ll/vuxqbOljVT/QT/ey8AdAl7HH/Ed+v1i5zVLYzkERfupkq9VdhcR1QAcRtcCgYEArxDpi+QgsVLo5GZItuOD61brZStKkIFsWdZnGFW7lg4zhEJibpMCwHDzo0VFlvBA08B4dAteBczNBGrDDiWSri9TzwmiBt0fmz6W7YaI/8tgpROk7UyxyiC5hZU9/ojhdJMtG7SMNwCXT15xsscpgrAr06Sdf+UqsC9PT75s0XcCgYEAxcHFlKbqxyX0R1HKDj9rUBQNBYbMQ3j03Hu4axGhUd0jAQC6xB4O4fRQPeNMziYTOag5mxGG+295zblm71s8HEsSKOzQeymALYPCGtNcGR/k75kSFB7eOAwl2b/AJS9HWPWyUA3Jm8OK06VISaKSnjtEG5fjeWrO+IUnKnd3bVU=',
    'log'             => [
      'file'  => storage_path('logs/alipay.log'),
    ],
  ],
  'wechat'  => [
    'app_id'          => 'wx******',  // 公众号 appid
    'mch_id'          => '14******',  // 商户号
    'key'             => '********',  // API 密钥
    'cert_client'     => resource_path('wechat_pay/apiclient_cert.pem'),
    'cert_key'        => resource_path('wechat_pay/apiclient_key.pem'),
    'log'             => [
      'file'  => storage_path('logs/wechat_pay.log'),
    ],
  ],
];