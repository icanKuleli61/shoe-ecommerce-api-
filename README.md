# 👟 SneakerSepeti - Full Stack E-Commerce System

Modern, mobil uyumlu ve RESTful mimari ile geliştirilmiş tam kapsamlı ayakkabı e-ticaret sistemi.

---

# 📌 Proje Hakkında

SneakerSepeti, kullanıcıların ürünleri görüntüleyebildiği, sepete ekleyebildiği, favorilere alabileceği, sipariş verebildiği ve sipariş süreçlerini takip edebildiği modern bir e-ticaret platformudur.

Proje backend ve frontend olmak üzere iki ayrı yapıdan oluşmaktadır.

Backend tarafında Laravel RESTful API mimarisi kullanılmıştır.  
Frontend tarafında HTML, CSS, JavaScript ve Bootstrap kullanılmıştır.

Sistem JWT Authentication altyapısı ile güvenli hale getirilmiştir.

---

# 🚀 Özellikler

## 👤 Kullanıcı Sistemi

- Kullanıcı kayıt sistemi
- Kullanıcı giriş sistemi
- JWT Authentication
- Profil yönetimi
- Şifre değiştirme
- Adres yönetimi

---

## 🛍 Ürün Sistemi

- Ürün listeleme
- Ürün detay sayfası
- Ürün varyant sistemi
- Renk seçimi
- Numara seçimi
- Stok kontrol sistemi
- Ürün görsel galerisi
- Banner sistemi

---

## ❤️ Favori Sistemi

- Favorilere ürün ekleme
- Favorilerden kaldırma
- Kullanıcı bazlı favori yönetimi

---

## 🛒 Sepet Sistemi

- Sepete ürün ekleme
- Adet artırma / azaltma
- Dinamik fiyat hesaplama
- Stok kontrolü
- Sepet toplam hesaplama

---

## 📦 Sipariş Sistemi

- Sipariş oluşturma
- Sipariş detay görüntüleme
- Sipariş geçmişi
- Sipariş durum takibi
- Admin sipariş yönetimi

---

## 💳 Cüzdan Sistemi

- Kullanıcı bakiyesi
- Bakiye işlemleri
- Cüzdan hareket geçmişi

---

## 🔐 Güvenlik

- JWT Middleware koruması
- Yetkisiz işlem kontrolü
- Token doğrulama sistemi

---

# 🛠 Kullanılan Teknolojiler

## Backend

- Laravel
- PHP
- PostgreSQL
- JWT Auth
- RESTful API
- Render Deployment

---

## Frontend

- HTML5
- CSS3
- JavaScript
- Bootstrap
- Responsive Design

---

# 🧠 Sistem Mimarisi

Sistem frontend ve backend olarak iki ayrı katmanda geliştirilmiştir.

Frontend katmanında kullanıcı arayüzü yönetilmektedir.

Backend katmanında:

- Authentication işlemleri
- Veritabanı yönetimi
- API işlemleri
- Sipariş süreçleri
- İş kuralları

yönetilmektedir.

Frontend ve backend haberleşmesi REST API üzerinden gerçekleştirilmektedir.

---

# 📂 Proje Klasör Yapısı

```bash
app/
│
├── Enums/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
│
├── Models/
├── Providers/
└── Services/

database/
│
├── migrations/
├── seeders/
└── factories/

routes/
│
├── api.php
├── web.php
└── console.php

resources/
storage/
public/
tests/
