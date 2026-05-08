📋 **วิธีแก้ไข Google Unsafe Content Warning**

## ⚠️ ปัญหา
Google พบว่าเว็บไซต์มีเนื้อหา unsafe:
- โพสต์เต็มไปด้วย Cracks, Keygens, Activators ของซอฟต์แวร์
- ผู้ใช้เสี่ยงต่ออันตรายจากมัลแวร์และ phishing

---

## 🔧 ขั้นตอนแก้ไข (Production Host)

### **1️⃣ เข้า SSH ที่ Plesk Host**
```bash
ssh username@your-host.com
```

### **2️⃣ Backup ฐานข้อมูล**
```bash
# ตรวจสอบ database name ก่อน
mysql -u dbuser -p -e "SHOW DATABASES;" | grep kv

# Backup database (ก่อนลบ)
mysqldump -u dbuser -p DB-KV-ELECTRONICS > backup-before-delete.sql
```

### **3️⃣ รัน SQL Script ลบโพสต์ Cracks**

**วิธี A: PhpMyAdmin (ง่ายสุด)**
1. ไปที่ Plesk Control Panel
2. ไปที่ **Database** → PhpMyAdmin
3. เลือกฐานข้อมูล `DB-KV-ELECTRONICS`
4. กดแท็บ **SQL**
5. Copy-paste เนื้อหาจากไฟล์ `delete-cracked-posts.sql` ลงในช่อง query
6. กดปุ่ม **Go** / **Execute**

**วิธี B: Command Line (เร็ว)**
```bash
mysql -u dbuser -p DB-KV-ELECTRONICS < delete-cracked-posts.sql
```

### **4️⃣ ลบไฟล์ Uploads ที่เกี่ยวข้อง**
```bash
# ลบไฟล์ PDF/ZIP ของ cracks ในโฟลเดอร์ uploads
cd /var/www/kv-electronics/wp-content/uploads/
find . -name "*crack*" -o -name "*keygen*" -o -name "*activator*" | xargs rm -f

# หรือใช้ Plesk File Manager GUI เพื่อลบด้วยตัวเอง
```

### **5️⃣ ส่งคำขอสอบทานจาก Google**
1. ไปที่ **Google Search Console**
2. เลือกเว็บไซต์
3. ไปที่ **Security Issues** ในเมนูด้านซ้าย
4. กดปุ่ม **Request a Review**
5. ใส่ข้อความ: "Removed all cracked software content and keygens from website"
6. กดส่ง

---

## ✅ การตรวจสอบผลลัพธ์

**เช็คจำนวนโพสต์เหลืออยู่:**
```sql
SELECT COUNT(*) as remaining_posts 
FROM 7bmcdm_posts 
WHERE post_type='post' 
AND post_status='publish' 
AND (post_title LIKE '%Crack%' OR post_title LIKE '%Keygen%');
```

ควรได้ผลลัพธ์เป็น `0`

---

## 📝 หมายเหตุสำคัญ
- ✅ ไฟล์ script SQL ได้ลำดับขั้นตอน สำเร็จ
- ✅ จะลบ postmeta, comments และ term_relationships ด้วย
- ✅ Optimize database หลังลบเสร็จ
- ⚠️ ต้องใช้ credentials ของ database จริง
- ⚠️ ลองทำ backup ก่อน แล้วค่อย run script

---

## 🚀 หลังจากแก้เสร็จ
1. ยา WordPress
2. ตรวจสอบหน้าแรกว่าปกติ
3. ส่งคำขอ Google Search Console (ข้างบน)
4. รอ Google ตรวจสอบ (3-7 วัน)

---

**ไฟล์ SQL script ที่ต้องใช้:** `delete-cracked-posts.sql`
