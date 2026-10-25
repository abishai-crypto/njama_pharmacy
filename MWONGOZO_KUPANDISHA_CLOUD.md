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
| **Inventory Officer (Stoo)** | `inventory` au `inventory@njamapharmacy.co.tz` | `Store@12345` |
| **Auditor (Mkaguzi Hesabu)** | `auditor` au `auditor@njamapharmacy.co.tz` | `Audit@12345` |

---

### 💡 Kidokezo Muhimu Kuhusu Free Tier ya Render:
- Seva ya bure ya Render huwa inalala (spin down) iwapo hakuna mtumiaji aliyetembelea ndani ya dakika 15.
- Mtu akifungua link kwa mara ya kwanza baada ya kulala, itachukua kama sekunde 30 hadi 45 kuamka, kisha baada ya hapo inafanya kazi kwa kasi ya kawaida.
- Ukitaka kuweka Database ya MySQL mtandaoni badala ya SQLite, unaweza kutumia **TiDB Cloud Serverless** (inatoa MySQL 8 bure ya 5GB milele bila kadi ya benki).
