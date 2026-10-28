# MWONGOZO WA KUPANDISHA MFUMO WA NJAMA PHARMACY KWENYE CLOUD (BURE & STRONG)

Mwongozo huu unakuelekeza hatua kwa hatua jinsi ya kupandisha mfumo wa **NJAMA PHARMACY** hewani mtandaoni bila gharama yoyote (FREE), ukiwa thabiti (STRONG) na unaounganishwa moja kwa moja na GitHub (`https://github.com/abishai-crypto/njama_pharmacy`).

---

## 🚀 NJIA ILIYOPENDEKEZWA: Render.com (Bure Kabisa na Salama)

**Render** ndio jukwaa bora na imara zaidi duniani kwa sasa kwa miradi ya GitHub:
- Inaunganishwa moja kwa moja na GitHub repo yako.
- Kila unapofanya `git push origin main`, inajisasisha (Auto-deploy) bila wewe kugusa chochote.
- Inatoa cheti cha usalama cha **Free SSL (HTTPS)** bure kabisa.
- Tayari tumekuandalia `Dockerfile` na `render.yaml` hivyo huna haja ya kusanidi seva (Zero-config).

---

### HATUA YA 1: Tuma Mabadiliko Mapya GitHub

Kwenye terminal yako hapa kwenye mashine, endesha amri hizi kutuma mafaili ya Docker na Render tuliyotengeneza:

```bash
git add .
git commit -m "Add Docker and Render cloud deployment configuration"
git push origin main
```

---

### HATUA YA 2: Jisajili / Ingia Render.com

1. Fungua browser yako na nenda: **[https://render.com](https://render.com)**
2. Bonyeza **Sign Up** au **Sign In** kwa kutumia akaunti yako ya **GitHub** (`abishai-crypto`).

---

### HATUA YA 3: Unganisha Mfumo (Deploy Web Service)

#### Chaguo A (Rahisi Zaidi - Blueprint):
1. Ukiwa kwenye Dashboard ya Render, bonyeza **New +** (juu kulia) kisha chagua **Blueprint**.
2. Chagua repository yako: `njama_pharmacy`.
3. Render itasoma faili la `render.yaml` kiotomatiki na kuweka mipangilio yote.
4. Bonyeza **Apply**!

#### Chaguo B (Web Service ya Kawaida):
1. Bonyeza **New +** -> **Web Service**.
2. Chagua **Build and deploy from a Git repository** -> Bonyeza **Next**.
3. Chagua repository yako: `njama_pharmacy` (au `abishai-crypto/njama_pharmacy`).
4. Jaza yafuatayo:
   - **Name**: `njama-pharmacy`
   - **Region**: `Frankfurt (EU)` (iko karibu zaidi na Afrika/Tanzania)
   - **Branch**: `main`
   - **Runtime**: `Docker` (usichague Node au PHP ya kawaida, chagua Docker ili itumie Dockerfile yetu thabiti)
   - **Instance Type**: `Free`
5. Chini kwenye **Environment Variables**, ongeza hizi:
   - `APP_NAME` = `NJAMA PHARMACY`
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `false`
   - `APP_KEY` = *(Unaweza kunakili APP_KEY kutoka kwenye .env yako ya sasa au kuacha tupu itatengenezwa moja kwa moja)*
   - `APP_TIMEZONE` = `Africa/Dar_es_Salaam`
   - `DB_CONNECTION` = `sqlite`
   - `DB_DATABASE` = `/var/www/html/database/database.sqlite`
6. Bonyeza kitufe kikubwa cha **Create Web Service**!

---

### HATUA YA 4: Nini Kinafanyika Wakati Inajenga (Build Process)?

Render itachukua kodi kutoka GitHub na kufanya yafuatayo moja kwa moja:
1. Itajenga mazingira ya PHP 8.2 na Apache.
2. Itaweka packages zote za Composer (`composer install`).
3. Itatengeneza database na kuendesha migrations zote (`php artisan migrate --force`).
4. Itaweka data zote za majaribio na dawa halisi (`php artisan db:seed --force`).
5. Itawasha mfumo na kukupa link ya bure kama vile:
   👉 **`https://njama-pharmacy-tmfu.onrender.com`**

---

### 🔑 Taarifa za Kuingilia (Login Credentials za Mteja):

Mteja wako anaweza kuingia na kujaribu mfumo kwa kutumia akaunti zifuatazo zilizopo tayari kwenye mfumo:

| Jukumu (Role) | Username / Email | Nenosiri (Password) |
| :--- | :--- | :--- |
| **Admin (Dr. Njama)** | `dr.njama` au `admin@njamapharmacy.co.tz` | `Admin@12345` |
| **Admin (Technician - Abishai)** | `abishai` au `abishai@njamapharmacy.co.tz` | `Admin@12345` |
| **Pharmacist (Mtoa Dawa)** | `pharmacist` au `pharmacist@njamapharmacy.co.tz` | `Pharm@12345` |
| **Cashier (Muuzaji - POS)** | `cashier` au `cashier@njamapharmacy.co.tz` | `Cash@12345` |
| **Auditor (Mkaguzi Hesabu)** | `auditor` au `auditor@njamapharmacy.co.tz` | `Audit@12345` |

### 🛡️ Usimamizi wa Nenosiri (Password Management):
- Msimamizi Mkuu (**Admin**) ana uwezo kamili wa kubadilisha nenosiri (password) la mfanyakazi yeyote wakati wowote moja kwa moja kupitia menyu ya **Wafanyakazi (Users)**.
- Bonyeza kitufe cha manjano cha **"Nenosiri"** pembeni ya jina la mfanyakazi, weka nenosiri jipya au bonyeza **"Tengeneza Nenosiri Nguvu Kiotomatiki"** kisha hifadhi.

---

## 🚨 KWANINI DATA ZILIKUWA ZIKIFUTIKA DESKTOP IKIZIMWA NA KUWASHWA?

### Sababu ya Kitaalamu (The Root Cause):
1. **Seva ya Bure ya Render haina Hard Disk ya Kudumu (Ephemeral Disk):**
   - Wakati mfumo ulipowekwa mtandaoni kwa mara ya kwanza, ulisanidiwa kutumia **SQLite** (`DB_CONNECTION=sqlite`). SQLite inahifadhi taarifa kwenye faili moja lililopo ndani ya seva ya Render.
   - Kwenye mpango wa bure wa Render, ikiwa hakuna mtu anayetumia mfumo ndani ya dakika 15 (kama vile usiku desktop ikizimwa na duka kufungwa), Render inazima seva (**Spins Down / Sleep**).
   - Render inapozima seva, **inafuta na kuharibu kabisa faili hilo la SQLite** lililokuwa na mauzo yote ya siku hiyo!
   - Asubuhi desktop ikifunguliwa, Render inaamka kama mtoto mchanga aliyetoka kuzaliwa: inatengeneza database mpya tupu, inaunda dawa zote kama bidhaa mpya, na **mauzo yote yaliyopita yanapotea kabisa (inasoma hakuna kilichouzwa)**!

2. **Suluhisho Imara la Kudumu Duniani (The Permanent Solution):**
   - Tenganisha **Database** na seva ya Render!
   - Tumia Cloud Database huru ya **MySQL ya TiDB Cloud Serverless** (Bure 5GB milele, haizimi kamwe, haifuti data, na inaruhusu kompyuta ya dukani na simu ya Boss kuona data ileile kwa wakati mmoja).

---

## 🛠️ HATUA ZA KUPATA DATABASE YA KUDUMU (TiDB Cloud - Dakika 2 Pekee)

### Hatua A: Jisajili na Utengeneze Database
1. Fungua browser yako na nenda: **[https://tidbcloud.com](https://tidbcloud.com)**
2. Bonyeza **Sign in with Google** (au GitHub) uingie moja kwa moja.
3. Kwenye skrini, bofya kitufe cha **"Create Cluster"**.
4. Chagua **TiDB Serverless (Free Forever)** -> Chagua Region ya karibu (mfano: **AWS / Frankfurt (eu-central-1)** au nyingine yoyote).
5. Bonyeza **Create** (inachukua sekunde 5 tu kukamilika!).

### Hatua B: Pata Taarifa za Muunganisho (Connection Details)
1. Baada ya cluster kukamilika, bonyeza kitufe cha **"Connect"** (juu kulia).
2. Chini ya **Connect with**, chagua **General / MySQL Client**.
3. Utaona taarifa zifuatazo (zihifadhi au uziweke wazi):
   - **Host:** (mfano: `gateway01.eu-central-1.prod.aws.tidbcloud.com`)
   - **Port:** `4000`
   - **User:** (mfano: `2ABCxxx.root`)
   - **Password:** (bonyeza kitufe cha **Generate Password** kisha nakili nenosiri hilo).
   - **Database:** `njama_pharmacy` *(kwenye tab ya "SQL Editor" ya TiDB, andika: `CREATE DATABASE njama_pharmacy;` kisha bofya Run)*.

### Hatua C: Bandika Taarifa Hizo Kwenye Render Dashboard
1. Ingia kwenye Dashboard yako ya **[Render.com](https://dashboard.render.com)**.
2. Bonyeza Web Service yako ya **`njama-pharmacy`**.
3. Kwenye menyu ya kushoto, bonyeza **Environment**.
4. Badilisha au Ongeza Variables hizi:
   - `DB_CONNECTION` = `mysql`
   - `DB_HOST` = *(Bandika Host yako ya TiDB hapa)*
   - `DB_PORT` = `4000`
   - `DB_DATABASE` = `njama_pharmacy`
   - `DB_USERNAME` = *(Bandika User wako wa TiDB hapa)*
   - `DB_PASSWORD` = *(Bandika Password uliyopewa hapa)*
   - `MYSQL_ATTR_SSL_CA` = `/etc/ssl/certs/ca-certificates.crt`
5. Bonyeza **Save Changes**!

---

### 🎉 MATOKEO BAADA YA KUBADILISHA HII:
- Render itajenga na kuunganisha mfumo na TiDB Cloud kiotomatiki.
- Kila dawa inayouzwa dukani inatunzwa moja kwa moja kwenye Cloud Database ya TiDB.
- **Hata desktop izimwe kwa siku 10, ikifunguliwa mauzo yote yapo salama 100%!**
- **Boss (Dr. Njama) akifungua simu yake akiwa popote, anaona kila senti iliyouzwa bila kukosa hata senti moja!**
