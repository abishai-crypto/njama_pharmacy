# 🏥 MWONGOZO WA MTUMIAJI — NJAMA PHARMACY
**Mwongozo Kamili wa Uendeshaji wa Duka la Dawa kwa Msimamizi Mkuu / Mmiliki (Boss)**

---

### Karibu Kwenye Mfumo Wako wa NJAMA PHARMACY!
Mfumo huu umetengenezwa mahsusi kwa ajili ya kuendesha shughuli zote za duka lako la dawa kwa ufanisi wa hali ya juu, kuongeza faida, kuziba mianya ya upotevu wa fedha na dawa, na kukidhi viwango vyote vya **TMDA** na **Bodi ya Famasi ya Tanzania (Pharmacy Council)**.

---

## 📑 YALIYOMO:
1. [Jinsi ya Kuingia Kwenye Mfumo (Login)](#1-jinsi-ya-kuingia-kwenye-mfumo-login)
2. [Dashibodi Kuu (Dashboard — Taswira ya Biashara Yako)](#2-dashibodi-kuu-dashboard)
3. [Dirisha la Mauzo (POS — Point of Sale)](#3-dirisha-la-mauzo-pos)
4. [Usimamizi wa Zamu za Fedha (Cashier Shifts)](#4-usimamizi-wa-zamu-za-fedha-cashier-shifts)
5. [Uwezo wa Kufuta Mauzo Yaliyofanyika Kimakosa (Admin Sale Deletion)](#5-kufuta-mauzo-yaliyofanyika-kimakosa)
6. [Kupanga na Kubadilisha Bei za Dawa (Bulk Selling Prices)](#6-kupanga-na-kubadilisha-bei-za-dawa)
7. [Stoo, Mapokezi ya Dawa na Wasambazaji (Purchases & Suppliers)](#7-stoo-mapokezi-ya-dawa-na-wasambazaji)
8. [Usimamizi wa Muda wa Dawa Kuisha (FEFO & Expiry Tracking)](#8-usimamizi-wa-dawa-zinazoisha-muda-wake-fefo)
9. [Kurekodi Matumizi ya Kila Siku (Operating Expenses)](#9-kurekodi-matumizi-ya-kila-siku-expenses)
10. [Ripoti za Faida na Hasara (Profit & Loss Reports)](#10-ripoti-za-faida-na-hasara)
11. [Usimamizi wa Wafanyakazi na Password Zao (User Management)](#11-usimamizi-wa-wafanyakazi-na-password)
12. [Hifadhi ya Kumbukumbu (Database Backups) na Msaada wa Kiufundi](#12-hifadhi-ya-kumbukumbu-na-msaada)

---

## 1. Jinsi ya Kuingia Kwenye Mfumo (Login)
1. Fungua tovuti ya mfumo wako kupitia kivinjari chochote (Google Chrome inapendekezwa):
   - **Link ya Mtandaoni:** `https://njama-pharmacy-tmfu.onrender.com`
   - **Kwenye Kompyuta ya Dukani (Localhost):** `http://127.0.0.1:8000`
2. Weka **Username** au **Barua Pepe (Email)** yako.
3. Weka **Nenosiri (Password)** lako.
4. Bonyeza kitufe cha kijani cha **"Sign In to NJAMA PHARMACY"**.

> [!TIP]
> **Usiri wa Taarifa:** Ukurasa wa kuingilia hauna taarifa zozote zilizojazwa mapema. Wewe (Boss) na wafanyakazi wako kila mmoja ataingia kwa taarifa zake binafsi.

---

## 2. Dashibodi Kuu (Dashboard)
Dashibodi ndiyo chumba chako cha amri (Cockpit). Ukifungua mfumo asubuhi au popote ulipo kwenye simu/kompyuta yako, unaona mara moja:
- **Period Filter (Kichujio cha Muda):** Unaweza kuchagua kuangalia biashara ya **Leo (Today)**, **Jana (Yesterday)**, **Wiki Hii (This Week)**, au **Mwezi Huu (This Month)**.
- **Kadi za Fedha (Unaziona Wewe Pekee na Mkaguzi wa Hesabu):**
  1. **Sales Revenue (Mauzo Yote):** Jumla ya pesa zote zilizoingia kupitia mauzo.
  2. **Cost of Goods Sold - COGS (Gharama ya Dawa):** Thamani ya dawa zilizouzwa kwa bei uliyonunulia kwa msambazaji.
  3. **Gross Profit (Faida Ghafi):** Pesa ya faida iliyopatikana kwenye dawa zilizouzwa (Mauzo ukitoa Gharama za Dawa) na asilimia ya faida (Profit Margin %).
  4. **Operating Expenses (Matumizi):** Pesa zote zilizotumika kulipia umeme, pango, chai, usafiri, n.k.
  5. **Estimated Net (Faida Halisi):** Faida halisi iliyobaki mfukoni (Gross Profit ukitoa Expenses).
  6. **Stock Value - Cost (Thamani ya Duka Lako):** Thamani ya jumla ya dawa zote zilizopo kwenye rafu na stoo kwa sasa kwa bei ya kununua na bei ya rejareja.
- **Chati ya Siku 7:** Inaonyesha mwelekeo wa mauzo na faida kwa kila siku ya wiki ili ujue siku gani biashara inachangamka zaidi.
- **Tahadhari za Haraka (Alerts):**
  - **Dawa Zilizopungua (Low Stock):** Dawa zilizobaki chache kwenye rafu ili uagize mapema.
  - **Dawa Zinazokaribia Kuisha Muda (Expiring in < 90 Days):** Huonyesha dawa za kupewa kipaumbele ziuzwe haraka kabla hazijaharibika.

> [!NOTE]
> **Usalama wa Taarifa za Faida:** Wafanyakazi wengine kama Mfamasia (Pharmacist) na Cashier hawawezi kuona kadi za COGS, Faida Ghafi, wala Mstari wa Faida kwenye chati. Wao wanaona idadi ya mauzo na miamala pekee.

---

## 3. Dirisha la Mauzo (POS — Point of Sale)
Dirisha hili linatumika kuuzia dawa kwa wateja wanaoingia dukani.
- Unaweza kulifungua kwa kubofya kitufe cha **"Open POS Terminal (F2)"** au kubonyeza kitufe cha **F2** kwenye kibodi.

### Hatua za Kufanya Mauzo:
1. **Tafuta Dawa:** Andika herufi 2 au 3 za jina la dawa (mfano: `amox`, `para`, `cipro`) au tumia mashine ya kuscan barcode. Mfumo utakuletea orodha papo hapo ikionyesha:
   - Jina la dawa na nguvu yake (Strength).
   - Bei ya kuuzia.
   - Idadi ya dawa zilizopo stoo.
   - Tarehe ya dawa inayoisha muda wake mapema zaidi.
2. **Ongeza Kwenye Kikapu:** Bofya dawa unayotaka au bonyeza `Enter`. Weka idadi (quantity) anayotaka mteja.
3. **Mteja (Hiari):** Kama mteja anataka jina lake liwepo kwenye risiti, jaza jina na namba yake ya simu. Kama ni mteja wa kawaida wa kupita, unaweza kuacha wazi.
4. **Chagua Njia ya Malipo:**
   - **Cash (Pesa Taslimu)**
   - **M-Pesa / Tigo Pesa / Airtel Money**
   - **Card / Benki**
   - **Split Payment (Mteja Akilipa nusu pesa taslimu na nusu simu)**
5. **Kamilisha Mauzo:** Bonyeza **"Kamilisha Mauzo (F9)"**.
6. **Kutoa Risiti:**
   - **Printa ya Joto (Thermal Receipt):** Inachapisha risiti ndogo ya kielektroniki yenye taarifa za duka, jina la dawa, bei, namba ya ankara na cashier.
   - **Digital Receipt (A4 / PDF):** Inafaa kwa wateja wanaotaka ankara rasmi au bima.

---

## 4. Usimamizi wa Zamu za Fedha (Cashier Shifts)
Ili kuzuia upotevu wa pesa au sintofahamu wakati wa kubadilishana zamu:
1. **Kufungua Zamu (Shift Opening):**
   - Kabla ya kuanza kuuza, Cashier hufungua zamu kwa kuingiza chenji aliyokabidhiwa drooni (mfano: `Tsh 50,000`).
2. **Kufunga Zamu (Shift Closing):**
   - Mwisho wa zamu au jioni, Cashier anahesabu pesa taslimu zilizopo drooni na kuziweka kwenye mfumo.
   - Mfumo unalinganisha pesa halisi dhidi ya mauzo yaliyorekodiwa:
     - Kama zimezidi: Inaonyesha **Pesa Zimezidi (+)**.
     - Kama zimepungua: Inaonyesha **Pesa Zimepungua (-)**.
     - Kama zinalingana: Inaonyesha **Safi (Balanced)**.
3. Hakuna muuzaji anayeweza kusema *"sikujua ziliingia ngapi"* kwa sababu kila muamala unahusishwa na zamu husika.

---

## 5. Kufuta Mauzo Yaliyofanyika Kimakosa
Kama mfanyakazi amefanya muamala kimakosa au mteja ameghairi na kuondoka kabla hajalipa:
1. Wewe kama **Admin** una mamlaka ya kufuta muamala huo.
2. Nenda kwenye **Reports > Sales Reports** au tazama jedwali la **Recent Sales** kwenye Dashibodi.
3. Kwenye safu ya vitendo, bofya kitufe chekundu cha **Tupa (Trash icon)**.
4. Sanduku la uthibitisho litafunguka likikuonyesha namba ya risiti na kiasi cha pesa.
5. Bofya **"Ndiyo, Futa Mauzo"**.

> [!IMPORTANT]
> **Nini Kinatokea Mauzo Yakifutwa?**
> - Dawa zote zilizokuwa zimeuzwa kwenye risiti hiyo **zinarudishwa moja kwa moja kwenye stoo (Restock)** kiotomatiki.
> - Jumla ya mauzo ya siku hiyo inapunguzwa ili ripoti yako ibaki safi na sahihi.
> - Tukio la ufutaji linahifadhiwa kwenye **Audit Log** likionyesha tarehe, saa, na kwamba umelifuta wewe.

---

## 6. Kupanga na Kubadilisha Bei za Dawa
Ili kubadilisha bei za kuuzia za dawa zako:
1. Nenda kwenye menyu ya **Administration > Bulk Selling Prices** (inafunguka kwa Admin pekee).
2. Utaona orodha ya dawa zako zote:
   - Inaonyesha **Bei Uliyonunulia (Purchase Cost)**.
   - Ina kisanduku cha **Bei ya Kuuzia (Selling Price)** ambapo unaweza kuandika bei mpya.
   - Inaonyesha papo hapo **Faida (Margin)** unayotengeneza kwa kila kidonge au chupa.
3. Baada ya kurekebisha bei, bofya kitufe cha chini cha **"Hifadhi Bei Zote Mpya (Save All)"**.
4. Bei zitasasishwa mara moja kwenye dirisha la mauzo (POS).

---

## 7. Stoo, Mapokezi ya Dawa na Wasambazaji
### A. Kusajili Msambazaji Mpya (Suppliers):
1. Nenda **Clinical & Procurement > Suppliers & Balances**.
2. Bonyeza **"Sajili Msambazaji Mpya"**.
3. Weka jina la msambazaji (mfano: `TONIC Pharmaceuticals`, `Anudha`, `Bahari Pharma`), namba ya simu, na anwani.

### B. Kupokea Mzigo Mpya wa Dawa (New Purchase):
1. Nenda **Purchases > Ankara Mpya (Receive Delivery)**.
2. Chagua msambazaji na uweke namba ya ankara yake (Invoice Number).
3. Chagua dawa unazopokea, weka **Batch Number**, **Tarehe ya Expiry**, **Idadi uliyopokea**, na **Bei ya kununua**.
4. Weka kiasi ulichomlipa msambazaji na kama umebakiwa na deni.
5. Bonyeza **Hifadhi**. Stoo ya dawa hizo itaongezeka kiotomatiki na deni la msambazaji litaingia kwenye kumbukumbu.

---

## 8. Usimamizi wa Dawa Zinazoisha Muda Wake (FEFO)
Mfumo huu unatumia kanuni ya kitaalamu ya **FEFO (First-Expiry, First-Out)**:
1. **Mauzo ya Kiotomatiki:** Mfumo unamzuia cashier kuuza dawa ya batch mpya ikiwa ipo batch ya zamani inayokaribia kuisha muda.
2. **Karantini ya Dawa:**
   - Dawa ikiisha muda wake au ikiharibika kabla ya kuuzwa, unaweza kuibonyeza na kuchagua **"Karantini"**.
   - Dawa ikiwa karantini, mfumo **unaizuia kabisa kuonekana kwenye dirisha la mauzo (POS)** ili kuzuia makosa ya kumuuzia mgonjwa dawa iliyoharibika.
3. **Ripoti ya Expiry:** Nenda **Reports > Expiry Analysis** kuona orodha ya dawa zitakazoisha ndani ya siku 30, siku 60, au siku 90 ili mzitoe ofa au kuziuza kwa kipaumbele.

---

## 9. Kurekodi Matumizi ya Kila Siku (Expenses)
Ili ujue faida halisi ya duka kila mwisho wa mwezi, unapaswa kurekodi matumizi madogo na makubwa:
1. Nenda **Finance & Reports > Operating Expenses**.
2. Bonyeza **"Rekodi Matumizi Mapya"**.
3. Chagua kundi la matumizi:
   - *Kodi ya Pango (Rent)*
   - *Umeme na Maji (Utilities)*
   - *Mishahara na Posho (Salaries / Allowances)*
   - *Vifaa vya Usafi na Ufungashaji (Packaging / Cleaning)*
   - *Leseni na TMDA (Regulatory Fees)*
   - *Matumizi Mengineyo (Other Expenses)*
4. Weka kiasi cha pesa (Tsh) na maelezo mafupi kisha bofya **Hifadhi**.
5. Kiasi hiki kitakatwa kiotomatiki kwenye ripoti ya Faida na Hasara (P&L).

---

## 10. Ripoti za Faida na Hasara
Wewe kama mmiliki, huna haja ya kupiga mahesabu magumu kwenye madaftari:
1. Nenda **Reports > Profit & Loss (P&L)**.
2. Chagua kipindi unachotaka (mfano: tarehe 1 hadi 31 ya mwezi).
3. Mfumo utakuletea jedwali safi linaloonyesha:
   - **Jumla ya Mapato ya Mauzo (Total Revenue)**
   - **Gharama za Dawa Zilizouzwa (Cost of Goods Sold)**
   - **Faida Ghafi ya Dawa (Gross Profit)**
   - **Jumla ya Matumizi ya Uendeshaji (Operating Expenses)**
   - **Faida Halisi Iliyobaki Mfukoni (Net Profit)**
4. Unaweza kubofya **"Pakua CSV (Excel)"** ili kutuma ripoti kwa mhasibu wako au kuitunza kwenye kompyuta yako.

---

## 11. Usimamizi wa Wafanyakazi na Password
Wewe ndiye msimamizi wa akaunti za wafanyakazi wote:
1. Nenda **Administration > Users**.
2. Utaona orodha ya wafanyakazi wako:
   - **Dr. Njama** (Admin — Wewe)
   - **Abishai Evaristo** (Admin — Mhandisi wako wa mifumo)
   - **Staff Pharmacist** (Mfamasia)
   - **Sales Cashier** (Muuzaji)
   - **Financial Auditor** (Mkaguzi)

### Jinsi ya Kubadilisha Password ya Mfanyakazi Yeyote:
1. Kwenye jina la mfanyakazi unayetaka kumbadilishia nenosiri, bonyeza kitufe cha njano cha **"🔑 Nenosiri"**.
2. Weka nenosiri jipya na ulithibitishe, au bofya kitufe cha *"Tengeneza Nenosiri Nguvu Kiotomatiki"* ili mfumo umchagulie password salama.
3. Bofya **"Sasisha Nenosiri"**.
4. Mpe mfanyakazi password hiyo mpya aanze kuitumia mara moja.

### Jinsi ya Kusajili Mfanyakazi Mpya:
- Bofya **"Sajili Mfanyakazi Mpya"**, jaza jina lake, username, namba ya simu, na umchagulie cheo chake (Cashier, Pharmacist, au Auditor).

---

## 12. Hifadhi ya Kumbukumbu na Msaada
### A. Kutengeneza Backup ya Database:
- Nenda **Administration > Database Backups**.
- Bofya **"Tengeneza Hifadhi Mpya (Create Backup)"**. Mfumo utatengeneza faili salama la database ambalo unaweza kulipakua (Download) na kuliweka kwenye flash drive yako.

### B. Msaada wa Dharura na Kiufundi (Technical Support):
Ikitokea changamoto yoyote ya kiufundi, unataka kuongezewa kitu kipya, au kompyuta ya dukani imepata hitilafu:
- **Mhandisi wa Mifumo:** Abishai Evaristo
- **Simu / WhatsApp:** `0656392400`
- **Barua Pepe:** `abishai@njamapharmacy.co.tz`

---
*Mwongozo huu umeandaliwa mahsusi kwa ajili ya Uongozi wa **NJAMA PHARMACY**, Dar es Salaam.*
