import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8081';

test.describe('Fix Verification', () => {

  test('Admin: Edit UTS/UAS Grades', async ({ page }) => {
    // Login as Admin
    await page.goto(`${BASE_URL}/`);
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');
    
    // Go to Input Nilai
    await page.goto(`${BASE_URL}/admin/nilai`);
    
    // Select first Jurusan
    await page.locator('a[href*="/admin/nilai/mata-pelajaran/"]').first().click();
    
    // Click "Input Nilai" on the first row/card
    // Assuming there is at least one class
    const inputButton = page.locator('a[href*="/admin/nilai/input/"]').first();
    await expect(inputButton).toBeVisible();
    await inputButton.click();
    
    // Wait for the table to load
    await page.waitForSelector('table tbody tr');
    
    // Input random value for UTS Sem 1
    const utsInput = page.locator('input[name*="[uts_sem1]"]').first();
    const uasInput = page.locator('input[name*="[uas_sem1]"]').first();
    
    const randomUTS = Math.floor(Math.random() * 100).toString();
    const randomUAS = Math.floor(Math.random() * 100).toString();
    
    await utsInput.fill(randomUTS);
    await uasInput.fill(randomUAS);
    
    // Save
    await page.click('button:has-text("Simpan Nilai")');
    
    // Wait for reload or navigation
    await page.waitForLoadState('networkidle');
    
    // Reload and check values
    await page.reload();
    await page.waitForSelector('table tbody tr');
    
    const actualUTS = await page.locator('input[name*="[uts_sem1]"]').first().inputValue();
    const actualUAS = await page.locator('input[name*="[uas_sem1]"]').first().inputValue();
    
    expect(parseFloat(actualUTS)).toBe(parseFloat(randomUTS));
    expect(parseFloat(actualUAS)).toBe(parseFloat(randomUAS));
  });

  test('Guru: Update Profile', async ({ page }) => {
    // Login as Guru
    await page.goto(`${BASE_URL}/`);
    await page.fill('input[name="username"]', 'guru1');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');
    
    // Go to Profile
    await page.goto(`${BASE_URL}/guru/user-pengguna`);
    
    // Edit NIP
    const randomNIP = Math.floor(Math.random() * 1000000000).toString();
    await page.fill('input[name="nip"]', randomNIP);
    
    // Edit Tempat Lahir
    const randomTempat = "City " + Math.floor(Math.random() * 1000);
    await page.fill('input[name="tempat_lahir"]', randomTempat);
    
    // Save
    await page.click('button:has-text("Update Profile")');
    
    // Wait for navigation
    await page.waitForLoadState('networkidle');
    
    // Reload to verify persistence
    await page.reload();
    await expect(page.locator('input[name="nip"]')).toHaveValue(randomNIP);
    await expect(page.locator('input[name="tempat_lahir"]')).toHaveValue(randomTempat);
  });

  test('Siswa: Update Profile', async ({ page }) => {
    // Login as Siswa
    await page.goto(`${BASE_URL}/`);
    await page.fill('input[name="username"]', 'testsiswa2');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');
    
    // Go to Profile
    await page.goto(`${BASE_URL}/siswa/user-pengguna`);
    
    // Edit NIS
    const randomNIS = Math.floor(Math.random() * 1000000000).toString();
    await page.fill('input[name="nis"]', randomNIS);
    
    // Edit Tempat Lahir
    const randomTempat = "City " + Math.floor(Math.random() * 1000);
    await page.fill('input[name="tempat_lahir"]', randomTempat);
    
    // Save
    await page.click('button:has-text("Update Profile")');
    
    // Wait for navigation
    await page.waitForLoadState('networkidle');
    
    // Reload to verify persistence
    await page.reload();
    await expect(page.locator('input[name="nis"]')).toHaveValue(randomNIS);
    await expect(page.locator('input[name="tempat_lahir"]')).toHaveValue(randomTempat);
  });

});
