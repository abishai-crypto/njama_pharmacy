# NJAMA PHARMACY MANAGEMENT SYSTEM (TANZANIA)
**Mfumo Kamili wa Kisasa wa Uendeshaji wa Duka la Dawa la Rejareja**

---

### 🏥 Kuhusu Mradi (Project Overview)
**NJAMA PHARMACY** ni mfumo wa kisasa, salama, na thabiti wa kusimamia biashara nzima ya duka la dawa la rejareja nchini Tanzania kwa kuzingatia miongozo na viwango vya TMDA (Tanzania Medicines and Medical Devices Authority) na Bodi ya Famasi (Pharmacy Council of Tanzania).

- **Duka la Dawa:** NJAMA PHARMACY
- **Daktari Msimamizi / Mmiliki:** Dr. Njama (Simu: `0715362428`)
- **Mhandisi wa Mifumo / Technician:** Abishai Evaristo (Simu: `0656392400`)
- **Msambazaji wa Awali wa Dawa:** TONIC Pharmaceuticals & Medical Supplies (Ankara ya 20 Oktoba 2026 — Thamani ya Jumla: `Tsh 1,308,596.00`)
- **Sarafu:** Shilingi ya Tanzania (`TZS` / `Tsh`)
- **Saa / Timezone:** `Africa/Dar_es_Salaam` (EAT)
- **Lugha:** Kiswahili na Kiingereza (Toggle: `/lang/sw` au `/lang/en`)

---

### 🚀 Sifa Kuu za Mfumo (Core Features)

1. **Stoo na Dawa Zote 180 (Full Medicine Catalog):**
   - Dawa zote 180 kutoka kwenye ankara ya msambazaji TONIC (Orodha Kuu 1–167 na Orodha ya Ziada 1–13) zimeingizwa kikamilifu zikiwa na namba za batch, tarehe halisi za mwisho wa matumizi (expiry dates), vipimo (UOM), na gharama ya ununuzi inayofikia jumla sahihi ya **Tsh 1,308,596.00**.

2. **Injini ya FEFO (First-Expiry, First-Out):**
   - Mfumo unahakikisha dawa zinazokaribia kuisha muda wake (earliest expiry) ndizo zinazotolewa kwanza wakati wa mauzo.
   - Huzuia kabisa kutoa batch mpya ikiwa ipo batch ya zamani inayokaribia kuisha muda.
   - Huzuia moja kwa moja (block) uuzaji wa dawa yoyote iliyopitwa na wakati (expired).

3. **Rejesta Isiyofutika ya Miondoko ya Stoo (Immutable Inventory Movement Ledger):**
   - Kila ongezeko au upungufu wa dawa (ununuzi, mauzo, marekebisho, karantini, write-off) linarekodiwa kwenye jedwali la `inventory_movements` likiwa na hesabu ya salio lililobaki (`balance_after`) na gharama kwa kila kipimo (`unit_cost`).

4. **Kituo cha Mauzo cha POS (Point of Sale Terminal):**
   - Utafutaji wa haraka kwa jina au barcode scanner (F2 Search).
   - Njia za mkato za kibodi (Keyboard shortcuts: F2 kwa kutafuta, F9 kwa kukamilisha malipo, ESC kwa kufuta).
   - Risiti mbili: Risiti ya Printa ya Joto (58mm/80mm Thermal Receipt) na Risiti ya Kidijitali (A4 / PDF).
   - Uwezo wa kufanya kazi mtandao ukikatika (Offline Queueing kwa LocalStorage) ikiwa na uthibitisho wa kuzuia miamala kujirudia (Idempotency verification kupitia `offline_uuid`).

5. **Kisasisho cha Bei za Kuuzia kwa Mkupuo (Bulk Selling Price Updater):**
   - Skrini maalum (`/products-bulk-prices`) inayomwezesha Dr. Njama au Abishai kusasisha bei za rejareja za dawa zote 180 kwa kubofya mara moja.
   - Huhesabu kiotomatiki faida (margin) kwa kila dawa papo hapo kabla ya kuhifadhi.
   - Kila mabadiliko ya bei yanarekodiwa kwenye historia (`product_price_histories`) pamoja na mtumiaji aliyebadili na sababu.

6. **Hati za Daktari na Utoaji Dawa (Prescriptions & Dispensing Records):**
   - Rekodi kamili ya jina la mgonjwa, umri, jinsia, namba ya simu, daktari mwandishi, hospitali, namba ya usajili wa MCT na maelekezo ya dozi.
   - Ufuatiliaji maalum wa dawa zilizowekewa vizuizi (Misoprostol, Oxytocin, Pregabalin, n.k.) kwa mujibu wa sheria za TMDA.

7. **Zamu za Wauzaji na Upatanisho wa Fedha (Cashier Shift Reconciliation):**
   - Kila muuzaji anafungua zamu kwa kuingiza chenji ya awali (opening float).
   - Wakati wa kufunga zamu, mfumo unalinganisha pesa taslimu zilizokusanywa dhidi ya zile zilizohesabiwa drooni na kuonyesha tofauti (overage/shortage).

8. **Ripoti za Kina za Kifedha (Financial Reports & Analytics):**
   - **Faida na Hasara (P&L Statement):** Mapato ya mauzo, gharama ya dawa (COGS), faida ghafi (gross profit), matumizi ya uendeshaji, na faida halisi (net profit).
   - **Dawa 15 Zinazoongoza kwa Faida:** Orodha ya dawa zinazozalisha faida kubwa zaidi.
   - **Thamani ya Stoo (Inventory Valuation):** Hesabu kamili ya thamani ya dawa kwa bei ya ununuzi na bei ya rejareja.
   - **Tahadhari za Expiry:** Dawa zilizopitwa na wakati, siku 30, siku 60, na siku 90.
   - **Pakua CSV:** Ripoti zote zinaweza kupakuliwa kama majedwali ya Excel/CSV.

9. **Usimamizi wa Wafanyakazi (Role-Based Access Control):**
   - Ngazi 5 za mamlaka za mfumo:
     1. `admin` (Dkt. Njama na Abishai) — Mamlaka kamili.
     2. `pharmacist` — POS, stoo, hati za daktari, utoaji dawa, manunuzi.
     3. `cashier` — POS na kufungua/kufunga zamu ya fedha pekee.
     4. `inventory_officer` — Upokeaji wa dawa, usimamizi wa stoo, na wasambazaji.
     5. `auditor` — Ukaguzi wa vitabu, ripoti za fedha, na audit logs (read-only).

10. **Nakala za Hifadhi (Database SQL Backups) na Audit Logs:**
    - Utengenezaji wa hifadhi ya database kwa kubofya kitufe kimoja (`/backups`).
    - Upakuaji salama wa faili za SQL dump.
    - Kumbukumbu zote za vitendo (Audit logs) zikirekodi tarehe, mtumiaji, kitendo, na anwani ya IP.

---

### 🔑 Akaunti za Kuingilia Mfumo (Pre-seeded Accounts)

| Mtumiaji | Jina la Kuingilia (Username) | Neno la Siri (Password) | Wajibu (Role) | Mamlaka |
| :--- | :--- | :--- | :--- | :--- |
| **Dr. Njama** | `dr.njama` | `Admin@12345` | `admin` | Mmiliki / Msimamizi Mkuu |
| **Abishai Evaristo** | `abishai` | `Admin@12345` | `admin` | Mhandisi wa Mifumo |
| **Mfamasia Mkuu** | `pharmacist` | `Pharm@12345` | `pharmacist` | Dawa, POS & Dispensing |
| **Cashier / Muuzaji** | `cashier` | `Cash@12345` | `cashier` | Dirisha la Mauzo (POS) |
| **Afisa Stoo** | `inventory` | `Store@12345` | `inventory_officer` | Mapokezi na Stoo |
| **Mkaguzi wa Hesabu** | `auditor` | `Audit@12345` | `auditor` | Ukaguzi wa Vitabu & Ripoti |

---

### 💻 Mahitaji na Ufungaji (Installation & Setup)

#### 1. Mahitaji ya Awali:
- PHP >= 8.2 (ikiwa na viendelezi vya `pdo_mysql`, `mbstring`, `bcmath`, `curl`, `json`)
- MySQL 8.0+ au MariaDB 10.4+
- Composer 2.x
- Git

#### 2. Kusanidi Mazingira (.env) na Ports:
Mfumo umesanidiwa kuunga mkono bandari (ports) zote mbili za kawaida za MySQL kwenye `localhost`:
- **Port 3307** (MariaDB / XAMPP ya kawaida)
- **Port 3306** (Huduma ya MySQL ya Windows / MySQL80)

Unaweza kubadili bandari kwenye faili la `.env` kwa kubadilisha thamani moja tu:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307        # Badili kuwa 3306 ikiwa MySQL yako inatumia 3306
DB_DATABASE=njama_pharmacy
DB_USERNAME=root
DB_PASSWORD=
```

#### 3. Kuanzisha Database na Kupakia Data:
```powershell
# Tengeneza database
mysql -u root -P 3307 -e "CREATE DATABASE IF NOT EXISTS njama_pharmacy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Endesha migrations
php artisan migrate

# Pakia dawa zote 180, ankara ya TONIC, watumiaji na mipangilio
php artisan db:seed
```

#### 4. Kuanzisha Mfumo Ndani ya Kituo cha Kazi (Local Server):
```powershell
php artisan serve --port=8000
```
Fungua kivinjari chako (Chrome, Edge, au Firefox) na uingie:
👉 **`http://localhost:8000`**

---

### 🧪 Majaribio ya Kiotomatiki (Automated Test Suite)
Mfumo una majaribio 19 ya kina (assertions 78) yanayothibitisha:
- Ulinzi na uthibitishaji wa watumiaji (Authentication & Role Authorization).
- Mauzo ya POS na ukataji wa dawa kwa kanuni ya FEFO.
- Uzuiaji wa uuzaji wa dawa zilizopitwa na wakati.
- Usasishaji wa bei za dawa kwa mkupuo.
- Hati za daktari na dawa zilizowekewa vizuizi.
- Ripoti za faida, hasara na thamani ya stoo.

Ili kuendesha majaribio:
```powershell
php artisan test
```

---

### ⚙️ Miongozo ya Uzalishaji na Matengenezo (Maintenance)

1. **Kuhifadhi Nakala ya Database (Backup):**
   - Ingia kama `dr.njama` au `abishai`, nenda kwenye menyu ya **Backups** (`/backups`) na ubofye kitufe cha kijani cha **"Tengeneza Backup Mpya Sasa"**.
   - Faili ya SQL itatengenezwa ndani ya `storage/app/backups/`. Bofya "Pakua" kuihifadhi kwenye flash au kompyuta nyingine.

2. **Kuweka Mfumo kwenye Mtandao wa Ndani (LAN Deployment):**
   Ikiwa pharmacy ina kompyuta zaidi ya moja (mfano: Server ya ofisini, Kompyuta ya Dirisha la Cashier, na Kompyuta ya Daktari):
   ```powershell
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   Kompyuta nyingine zilizopo kwenye Wi-Fi au Switch moja zitaweza kufungua mfumo kupitia anwani ya IP ya server (mfano: `http://192.168.1.100:8000`).

---

&copy; 2026 **NJAMA PHARMACY** — Dar es Salaam, Tanzania.  
Usaidizi wa Kiufundi: **Abishai Evaristo (0656392400)** | Daktari Msimamizi: **Dr. Njama (0715362428)**
