from selenium import webdriver

driver = webdriver.Chrome()
driver.get("https://www.instagram.com")
print(driver.title)
driver.quit()

