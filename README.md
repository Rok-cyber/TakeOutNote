# Project-TakeOutNote
PHP & MySQL로 구현한 이커머스 샵과 클라우드 노트 서비스 포트폴리오

# TakeOutNote & E-Commerce Shop Portfolio

> A combined portfolio project showcasing a plain-PHP e-commerce shop and a cloud-notes service, designed to demonstrate end-to-end CRUD flows, RESTful APIs, and custom session/JWT authentication—all without a framework.

> **이 프로젝트는 주식회사 mindelevation에 허가를 받고 업로드한 것이며, 저작권은 mindelevation에 있습니다.**  
> **서버에서 직접 다운로드 후 테스트해보고 싶으시면** rokbnoc@gmail.com **으로 메일 주시면 키 코드를 제공해드립니다.**

---

## Table of Contents

1. [프로젝트 개요](#프로젝트-개요)  
2. [기술 스택](#기술-스택)  
3. [핵심 기능](#핵심-기능)  
4. [데이터베이스 스키마](#데이터베이스-스키마)  
5. [로드맵](#로드맵)  
6. [기여 가이드](#기여-가이드)  
7. [라이선스](#라이선스)  

---

## 프로젝트 개요

이 리포지토리는 두 가지 서비스를 하나의 포트폴리오로 통합합니다:

- **E-Commerce Shop**  
  - **주요 흐름**: 상품 목록 → 상세 페이지 → 장바구니 → 주문 생성 → 결제(가상) → 주문 내역 조회  
  - **관리자 기능**: 상품 등록·수정·삭제, 주문 상태 조회·관리  

- **Cloud Note Service**  
  - **주요 흐름**: 노트 생성·수정·삭제 → 폴더 관리 → 태그 기반 검색 → 이미지/파일 업로드  
  - **인증·권한**: 세션·JWT 기반 사용자 분리  

각 서비스는 별도의 URL 경로로 제공되며, 로그인 후 양쪽 모두 자유롭게 오갈 수 있도록 설계되었습니다.

---

## 기술 스택

- **언어 & 런타임**: PHP (CLI & built-in server)  
- **데이터베이스**: MySQL  
- **프론트엔드**: HTML5, CSS3, Vanilla JavaScript  
- **인증**: PHP 세션, JSON Web Token (JWT)  
- **배포**: AWS EC2, RDS (MySQL)  
- **버전 관리**: Git & GitHub  

---

## 핵심 기능

### 1. E-Commerce Shop
- **상품 탐색**: 카테고리별 필터, 페이징  
- **장바구니**: 추가·수량 변경·삭제  
- **주문 & 결제**: 페이퍼 프로토타입 가상 결제  
- **주문 내역**: 과거 주문 조회, 주문 상태 표시  
- **관리자 페이지**: 상품·주문 관리 UI  

### 2. Cloud Note Service
- **노트 CRUD**: 제목, 내용, 태그, 이미지/파일 첨부  
- **폴더 구조**: 사용자별 폴더 생성·이동  
- **태그 검색**: 태그별 노트 필터링  
- **권한 분리**: JWT 세션 인증, 사용자별 노트 접근 제어  
- **파일 업로드**: 최대 5MB, 서버 내 디렉터리 저장 및 DB 메타정보 관리  

---
## 로드맵

- **검색 고도화**: 전체 텍스트 검색(Full-Text Search) 및 자동 완성(Auto-Complete) 기능 추가  
- **관리자 대시보드 리팩토링**: UI/UX 개선 및 실시간 통계 차트 도입  
- **배포 자동화**: GitHub Actions → AWS EC2/RDS CI/CD 파이프라인 구축  
- **모바일 반응형 UI 개선**: 다양한 해상도 지원 및 PWA(Progressive Web App) 도입 검토  

---

## 기여 가이드

1. **이슈 생성**  
   - 새로운 기능 제안, 버그 리포트 등 이슈를 열어 주세요.  
2. **브랜치 네이밍**  
   - `feature/xxx` 또는 `fix/xxx` 형태로 브랜치를 생성합니다.  
3. **커밋 메시지**  
   - [Conventional Commits](https://www.conventionalcommits.org/) 규칙을 따릅니다.  
     - `feat:`, `fix:`, `docs:`, `refactor:` 등  
4. **풀 리퀘스트(PR) 작성**  
   - 변경 내용을 요약하여 PR을 생성하고, 리뷰어를 지정해 주세요.  
5. **코드 스타일**  
   - PSR-12 코딩 표준을 준수해 주세요.  
6. **테스트**  
   - 새로운 기능/버그 수정 시 관련 단위 테스트를 추가해 주세요.  

---

## 라이선스

- **코드**: MIT © 2025 Seongrok Lee  
- **저작권**: © 2025 mindelevation. All rights reserved.  

![Uploading image.png…]()
