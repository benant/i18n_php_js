# www/api/lib 폴더와 www/api/v1.0 폴더에서만 번역 대상 문구 추출합니다.

echo '' > file_list.txt
# 대상 파일 목록 생성
# 하위폴더 제외합니다.
# ls ../www/*.php >> file_list.txt
# 하위폴더 포함 검색
find ../www/ -iname "*.php" >> file_list.txt
find ../www/ -iname "*.html" >> file_list.txt
find ../www/ -iname "*.js" >> file_list.txt

# 번역 데이터 생성
xgettext --from-code=UTF-8 --default-domain=WWW --output-dir=. --output=WWW.pot --join-existing -f file_list.txt --language=PHP
xgettext --from-code=UTF-8 --default-domain=WWW --output-dir=. --output=WWW.pot --join-existing -f file_list.txt --language=PHP --keyword=_e
xgettext --from-code=UTF-8 --default-domain=WWW --output-dir=. --output=WWW.pot --join-existing -f file_list.txt --language=PHP --keyword=__
xgettext --from-code=UTF-8 --default-domain=WWW --output-dir=. --output=WWW.pot --join-existing -f file_list.txt --keyword=_e
xgettext --from-code=UTF-8 --default-domain=WWW --output-dir=. --output=WWW.pot --join-existing -f file_list.txt --keyword=__

# 대상 파일 목록 삭제
rm file_list.txt