import openpyxl

wb = openpyxl.load_workbook('C:/Users/ASUS/.gemini/antigravity/brain/c24b40aa-e4fb-4a14-b168-cc36eb96fa35/.user_uploaded/media_1795618693112_78240e55.xlsx', data_only=True)
print("Sheets:", wb.sheetnames)

for name in wb.sheetnames[:4]:
    sheet = wb[name]
    print(f"\n--- Sheet: {name} ---")
    for r in range(1, 6):
        row_vals = [sheet.cell(r, c).value for c in range(1, 8)]
        if any(row_vals):
            print(f"Row {r}: {row_vals}")
