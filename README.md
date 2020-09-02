# Пример организации кода на 1С-Битрикс

**master**: [img_badge]<br>
**production**: [img_badge]

<br>

# Требования
- php: >=7.1
- [composer](https://getcomposer.org/)
- make

<br>

# Установка


#### 1. Установить пакеты composer
```bash
composer install
```

#### 2. Создать .env
```bash
cp .env.example .env
```

#### 3. Создать символьные ссылки
##### linux / macos (bash)
```bash
ln -s ../../bitrix sites/s1/bitrix
ln -s ../../local sites/s1/local
ln -s ../../upload sites/s1/upload
```

##### windows (cmd)
```cmd
mklink /j "%CD%\sites\s1\local" "%CD%\local"
mklink /j "%CD%\sites\s1\bitrix" "%CD%\bitrix"
mklink /j "%CD%\sites\s1\upload" "%CD%\upload"
```

<br>

# Использование

##### Проверить код
```bash
make check
```
##### Исправить код
```bash
make fix
```