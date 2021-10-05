

## 常见问题



**获取开发包含使用当前用户的Controller？**

使当前 `Controller` 实现 `Module\Member\Support\MemberLoginCheck` 接口，同时在 Route 中使用中间件 `WebAuthMiddleware`



**如何获取到当前登录用户？**

通过以下方法获取到当前用户

```php
\Module\Member\Auth\MemberUser::id()
\Module\Member\Auth\MemberUser::get()
```

