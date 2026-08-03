from PIL import Image, ImageDraw
import os

os.makedirs('public/icons', exist_ok=True)

for size in [192, 512]:
    img = Image.new('RGBA', (size, size), (17, 24, 39, 255))
    draw = ImageDraw.Draw(img)
    margin = size // 8
    radius = size // 8
    draw.rounded_rectangle(
        (margin, margin, size - margin - 1, size - margin - 1),
        radius=radius,
        fill=(17, 24, 39, 255),
        outline=(251, 191, 36, 255),
        width=max(6, size // 24),
    )
    draw.rectangle((size // 4, size // 4, size * 3 // 4, size * 3 // 4), fill=(251, 191, 36, 255))
    img.save(f'public/icons/icon-{size}.png')

print('created')
