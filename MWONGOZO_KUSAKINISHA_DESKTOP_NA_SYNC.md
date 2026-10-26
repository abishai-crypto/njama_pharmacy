# 🖥️ MWONGOZO WA HARAKA WA KUSAKINISHA MFUMO KWENYE DESKTOP MPYA (DUKANI) NA KUSYNC NA CLOUD

**Lengo:** Kufanya installation ya haraka (chini ya dakika 10) kwenye Desktop mpya ya duka ambayo haina environment yoyote (hakuna PHP, hakuna MySQL, hakuna Composer), kuifanya ifanye kazi **Offline** bila intaneti, na kuhakikisha inapokuwa **Online** inawasiliana na Cloud ili Boss (Dr. Njama) aone mauzo yote akiwa mbali.

---

## 🗂️ YALIYOMO:
1. [MAANDALIZI KWENYE LAPTOP YAKO SASA HIVI (Kabla Desktop Haijafika)](#hatua-1-maandalizi-kwenye-laptop-yako-sasa-hivi)
2. [KUSAKINISHA LARAGON KWENYE DESKTOP MPYA (Dakika 2)](#hatua-2-kusakinisha-laragon-kwenye-desktop-mpya)
3. [KUHAMISHA MRADI NA DATABASE KWENYE DESKTOP](#hatua-3-kuhamisha-mradi-na-database)
4. [KUWASHA MFUMO KWA 1-CLICK SHORCUT (ANZA_NJAMA_PHARMACY.bat)](#hatua-4-kuwasha-mfumo-kwa-1-click)
5. [NAMNA MAUZO YA OFFLINE YANAVYOENDA CLOUD ILI BOSS AONE AKIWA MBALI](#hatua-5-namna-mauzo-yanavyoingia-cloud-kwa-boss)
6. [UTATUZI WA HARAKA WA MATATIZO (TROUBLESHOOTING)](#hatua-6-utatuzi-wa-haraka-troubleshooting)

---

## HATUA 1: MAANDALIZI KWENYE LAPTOP YAKO SASA HIVI
Ili usipoteze muda desktop ikifika, andaa **Flash Drive (USB)** kwenye laptop yako sasa hivi ikiwa na vitu hivi vitatu (3):

### 1. Pakua Kisakinishi cha Laragon (Laragon Full Installer):
- **Kwanini Laragon?** Badala ya kupakua PHP, MySQL, Composer, Git kimoja kimoja na kuanza kupambana na kuset Environment Variables (PATH) au `php.ini`, Laragon inakuja na **PHP 8.2+, MySQL 8, Apache, Composer, Git na HeidiSQL** vyote vikiwa tayari ndani ya faili moja tu la ~140MB.
- Pakua faili la **"Laragon - Full (PHP 8.x, MySQL, Apache)"** kutoka:
  👉 `https://laragon.org/download/`
- Liweke kwenye Flash Drive yako.

### 2. Nakili Folda ya Mradi (`d:\DR.NJAMA PHARMACY`):
- Nakili (Copy) folda nzima ya `d:\DR.NJAMA PHARMACY` uiweke kwenye Flash Drive.
- **MUHIMU:** Hakikisha unanakili ikiwa na folda ya `vendor` na faili la `.env`! Hii inamaanisha kwenye desktop HAUTAHITAJI intaneti wala ku-run `composer install` — kila kitu kitafanya kazi papo hapo.

### 3. Nakili Faili la Database Tayari:
- Kwenye laptop yako tayari nimekutengenezea faili kamili la database safi lenye dawa zote, bei, na watumiaji bila mauzo ya majaribio:
  👉 `d:\DR.NJAMA PHARMACY\storage\app\backups\njama_pharmacy_fresh_install.sql`
- Likiweke pia kwenye Flash Drive yako.

---

## HATUA 2: KUSAKINISHA LARAGON KWENYE DESKTOP MPYA (Dakika 2)
1. Chomeka Flash Drive kwenye Desktop mpya.
2. Fungua faili la **`laragon-wamp.exe`** kisha bonyeza **Next > Next > Install**.
3. Baada ya kumaliza, ifungue **Laragon**.
4. Bonyeza kitufe cha **"Start All"** (kitawasha Apache na MySQL mara moja).

---

## HATUA 3: KUHAMISHA MRADI NA DATABASE

### A. Weka Mradi Kwenye Kompyuta:
1. Kutoka kwenye Flash Drive, nakili folda ya `DR.NJAMA PHARMACY` na uibandike (paste) kwenye:
   `C:\laragon\www\DR.NJAMA PHARMACY`  *(au moja kwa moja kwenye `C:\DR.NJAMA PHARMACY`)*.

### B. Tengeneza Database na Uingize (Import) Data:
1. Kwenye dirisha la **Laragon**, bofya kitufe cha **"Database"** (itafungua HeidiSQL) au bofya **"Terminal"**.
2. Kwenye Terminal ya Laragon, andika amri hizi mbili tu:
   ```bash
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS njama_pharmacy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```
3. Kisha ingiza faili la SQL uliloliandaa:
   ```bash
   mysql -u root njama_pharmacy < "C:\laragon\www\DR.NJAMA PHARMACY\storage\app\backups\njama_pharmacy_fresh_install.sql"
   ```
   *(Hii inachukua sekunde 5 tu, na database nzima inakuwa imekamilika!)*

### C. Hakikisha Faili la `.env` Kwenye Desktop:
Fungua faili la `C:\laragon\www\DR.NJAMA PHARMACY\.env` na uhakikishe mipangilio hii ya database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=njama_pharmacy
DB_USERNAME=root
DB_PASSWORD=
```
*(Kwenye Laragon, default port ni 3306 na password ni tupu).*

---

## HATUA 4: KUWASHA MFUMO KWA 1-CLICK (ANZA_NJAMA_PHARMACY.bat)
Ili mfamasia au muuzaji asipate shida kuandika command kila asubuhi:
1. Ndani ya folda ya `C:\laragon\www\DR.NJAMA PHARMACY\`, utaona faili ninalokutengenezea linaitwa **`ANZA_NJAMA_PHARMACY.bat`**.
2. Libofye kwa kubofya kitufe cha kulia cha panya (Right Click) > **Send to > Desktop (create shortcut)**.
3. Kwenye Desktop, ibadilishe jina shortcut hiyo iwe: **🏥 ANZA NJAMA PHARMACY**.
4. **Jaribu kuibonyeza (Double-Click):**
   - Itawasha server kiotomatiki kwa nyuma.
   - Itafungua kivinjari cha Google Chrome moja kwa moja kwenye `http://127.0.0.1:8000`.
   - Ukurasa safi wa Login wa NJAMA PHARMACY utatokea!

---

## HATUA 5: NAMNA MAUZO YANAVYOINGIA CLOUD ILI BOSS AONE AKIWA MBALI

Ili Boss (Dr. Njama) aone kinachoendelea popote alipo kupitia simu yake kwenye link ya mtandaoni (`https://njama-pharmacy-tmfu.onrender.com`), kuna mifumo miwili (2) imara:

### MFUMO 1: MATUMIZI YA KAWAIDA YA DUKANI (Cloud Primary + Offline Fallback)
*(Huu ndio mfumo rahisi na unaopendekezwa zaidi kama duka lina Wi-Fi au Hotspot ya simu):*
1. Kwenye Desktop ya dukani, muuzaji anafungua kivinjari na kuingia:
   👉 `https://njama-pharmacy-tmfu.onrender.com`
2. **Kama Intaneti ipo:**
   - Kila dawa inayouzwa inajirekodi mtandaoni papo hapo.
   - Boss akiangalia simu yake akiwa nyumbani au safarini anaona mauzo, pesa zilizoingia, na dawa zilizopungua kwa sekunde ile ile.
3. **Kama Intaneti ikikatika ghafla:**
   - Mfumo kwenye dirisha la mauzo (POS) una akili ya kutambua kuwa mtandao umekatika (**Offline Mode**).
   - Muuzaji anaendelea kuuza bila kukwama! Mauzo yanahifadhiwa salama kwenye kumbukumbu ya ndani ya kivinjari (*localStorage* na *IndexedDB*) na risiti inatoka kama kawaida.
   - Intaneti ikirudi: Kitufe cha **"Sync (X)"** kinawaka rangi ya njano juu ya POS. Muuzaji anakibonyeza au kinaji-sync chenyewe moja kwa moja, na mauzo yote yanapanda kwenye Cloud kwa sekunde 2 tu!

---

### MFUMO 2: LOCAL COUNTER + CLOUD DATABASE REPLICATION
*(Kama eneo la duka intaneti inasumbua mara kwa mara na mnataka kompyuta iwe inauzia kwenye Localhost `http://127.0.0.1:8000`):*
1. **Kuunganisha Localhost na Cloud Database ya Pamoja (TiDB Cloud Serverless):**
   - Unaweza kusajili bure database ya TiDB Cloud Serverless (MySQL ya bure mtandaoni).
   - Weka taarifa za TiDB kwenye `.env` ya Desktop na kwenye `.env` ya Render.
   - Desktop ikiwa na intaneti inatuma kila kitu moja kwa moja kwenye Cloud.
2. **Kama intaneti haipo kabisa:**
   - Desktop inatumia database yake ya ndani (`127.0.0.1`).
   - Mwisho wa siku au jioni intaneti ikipatikana, Pharmacist au wewe mnaweza kutuma backup au kusawazisha data kwa kubofya kitufe cha kusawazisha.

---

## HATUA 6: UTATUZI WA HARAKA (TROUBLESHOOTING)

| Tatizo | Sababu | Jinsi ya Kutatua |
| :--- | :--- | :--- |
| **`SQLSTATE[HY000] [2002] Connection refused`** | MySQL kwenye Laragon haijawashwa au Port ni tofauti | Fungua Laragon, bonyeza **"Start All"**. Hakikisha `.env` inasoma `DB_PORT=3306`. |
| **Kivinjari kinasema `Site can't be reached`** | Batch file ya kuanzisha server imefungwa | Bonyeza tena shortcut ya **ANZA NJAMA PHARMACY.bat** kwenye desktop. |
| **POS inasema "Offline Mode"** | Intaneti haipatikani kwenye kompyuta | Mfumo unaendelea kuuza kama kawaida. Unganisha Wi-Fi au simu, kisha bonyeza kitufe cha **"Sync"** kupandisha mauzo. |
| **Printa ya risiti haichapi** | Driver za printa ya USB hazijasakinishwa | Sakinisha driver za POS Thermal Printer (mfano: Xprinter / POS-58 / POS-80) na uweke kama *Default Printer* kwenye Windows. |

---

### Muhtasari wa Muda Utaotumia:
- **Kwenye Laptop Yako Sasa:** Dakika 3 (Kunakili mradi na kupakua Laragon kwenye Flash).
- **Desktop Ikifika Dukani:** Dakika 5 (Kusakinisha Laragon, ku-paste folda, ku-import database, na kuweka shortcut ya desktop).
- **Jumla:** Chini ya dakika 10 duka linakuwa hewani na linaanza kuuza mara moja!
