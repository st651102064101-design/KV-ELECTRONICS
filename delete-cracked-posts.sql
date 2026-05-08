-- ===============================================
-- CLEANUP: ลบบทความ Cracked Software ทั้งหมด
-- ===============================================
-- สำหรับ: kv-electronics.com (Production Host)
-- คำนำหน้าตาราง: 7bmcdm_
-- ===============================================

-- Step 1: เก็บ backup ID ของบทความที่จะลบ
SET @delete_ids = (
  SELECT GROUP_CONCAT(ID) 
  FROM 7bmcdm_posts 
  WHERE post_type='post' 
  AND post_status='publish'
  AND (
    post_title LIKE '%Crack%' 
    OR post_title LIKE '%Keygen%' 
    OR post_title LIKE '%Activator%'
    OR post_title LIKE '%Portable%crack%'
    OR post_title LIKE '%Portable%keygen%'
  )
);

-- Step 2: ลบ postmeta ที่เกี่ยวข้อง
DELETE FROM 7bmcdm_postmeta 
WHERE post_id IN (
  SELECT ID FROM 7bmcdm_posts 
  WHERE post_type='post' 
  AND post_status='publish'
  AND (
    post_title LIKE '%Crack%' 
    OR post_title LIKE '%Keygen%' 
    OR post_title LIKE '%Activator%'
    OR post_title LIKE '%Portable%crack%'
    OR post_title LIKE '%Portable%keygen%'
  )
);

-- Step 3: ลบ comments ที่เกี่ยวข้อง
DELETE FROM 7bmcdm_commentmeta 
WHERE comment_id IN (
  SELECT comment_ID FROM 7bmcdm_comments 
  WHERE comment_post_ID IN (
    SELECT ID FROM 7bmcdm_posts 
    WHERE post_type='post' 
    AND post_status='publish'
    AND (
      post_title LIKE '%Crack%' 
      OR post_title LIKE '%Keygen%' 
      OR post_title LIKE '%Activator%'
      OR post_title LIKE '%Portable%crack%'
      OR post_title LIKE '%Portable%keygen%'
    )
  )
);

-- Step 4: ลบ comments
DELETE FROM 7bmcdm_comments 
WHERE comment_post_ID IN (
  SELECT ID FROM 7bmcdm_posts 
  WHERE post_type='post' 
  AND post_status='publish'
  AND (
    post_title LIKE '%Crack%' 
    OR post_title LIKE '%Keygen%' 
    OR post_title LIKE '%Activator%'
    OR post_title LIKE '%Portable%crack%'
    OR post_title LIKE '%Portable%keygen%'
  )
);

-- Step 5: ลบ term_relationships (categories/tags)
DELETE FROM 7bmcdm_term_relationships 
WHERE object_id IN (
  SELECT ID FROM 7bmcdm_posts 
  WHERE post_type='post' 
  AND post_status='publish'
  AND (
    post_title LIKE '%Crack%' 
    OR post_title LIKE '%Keygen%' 
    OR post_title LIKE '%Activator%'
    OR post_title LIKE '%Portable%crack%'
    OR post_title LIKE '%Portable%keygen%'
  )
);

-- Step 6: ลบบทความ posts
DELETE FROM 7bmcdm_posts 
WHERE post_type='post' 
AND post_status='publish'
AND (
  post_title LIKE '%Crack%' 
  OR post_title LIKE '%Keygen%' 
  OR post_title LIKE '%Activator%'
  OR post_title LIKE '%Portable%crack%'
  OR post_title LIKE '%Portable%keygen%'
);

-- Step 7: ทำให้เรียบร้อย (optimize)
OPTIMIZE TABLE 7bmcdm_posts;
OPTIMIZE TABLE 7bmcdm_postmeta;
OPTIMIZE TABLE 7bmcdm_comments;
OPTIMIZE TABLE 7bmcdm_commentmeta;

-- สรุปผล
SELECT 'Done: ลบบทความ Cracked Software ทั้งหมดแล้ว' as status;
SELECT COUNT(*) as remaining_posts FROM 7bmcdm_posts WHERE post_type='post' AND post_status='publish' AND (post_title LIKE '%Crack%' OR post_title LIKE '%Keygen%');
