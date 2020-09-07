FROM php:7.4-cli-alpine@sha256:b8c0b0e436b6699ba8ca29fa575a4030f90345a30c159a2f27a62780941106e6 as builder

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json /usr/src/ptdls/composer.json

WORKDIR /usr/src/ptdls

RUN composer install --no-dev --optimize-autoloader

COPY . /usr/src/ptdls

FROM golang:1.15.0-alpine@sha256:73182a0a24a1534e31ad9cc9e3a4bb46bb030a883b26eda0a87060f679b83607 as linterverify
RUN apk add --update alpine-sdk

RUN go get -u github.com/VKCOM/noverify

WORKDIR /usr/src/ptdls
COPY --from=builder /usr/src/ptdls .
RUN  /go/bin/noverify -cache-dir=$HOME/tmp/cache/noverify -exclude='vendor/|tests/|tmp/|var/' ./

CMD [ "php", "./entrypoint.php" ]